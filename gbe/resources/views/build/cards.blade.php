@extends('templates.default')

@section('head')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <style>
        #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
        #sortable li { margin: 0px 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.2em;  }
        #sortable li span { position: absolute; margin-left: -1.3em; }

        #sortable .ui-selecting { background: #FECA40; }
        #sortable .ui-selected { background: #F39814; color: white; }
    </style>
@stop
@section('content')

    <div class="row">
        <h1>{!! $site->name !!} </h1>
    </div>
    @include('build.tabs', ['page'=>'cards'])
    <div class="row">
        <div class="col-xs-12">
            <button style="float:right; position:relative; right:50px; bottom:-20px;" class="btn btn-primary btn-sm" onclick="window.location.href='/build/{!! $site->slug !!}/cardsets/create'">Create New Card Set</button>
        </div>
    </div>


    <div class="row" style="padding-top:5px;">
        <div class="col-sm-5">
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="cardset-select" class="control-label">Select Card Set: </label>
                    <select id="cardset-select" name="cardset-select" class="form-control" onchange="return createCardList()">
                        @foreach($cardsets as $cardset)
                            <option value="{!! $cardset->id !!}"
                                    {!! $cardset->id == $selectedSet?'selected':' ' !!}>
                                {!! $cardset->name !!}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="col-sm-1" ></div>
        <div class="col-sm-6" >
            <button style="position:relative; right:20px; bottom:-30px;" class="btn btn-primary btn-sm" onclick="return addCard()">Add Card</button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <p id="list-header">No cards have been created for this card set.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-5">
            <ul id="sortable">
                <li>This is a stub.</li>
            </ul>

        </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-6">
            @foreach($cards as $card)
                <div class="card-detail" id="card_{!! $card->id !!}">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>{!! $card->title !!}</h3>
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-3">
                            <a style="position:relative; bottom:-20px; right:20px;"
                               href="/build/{!! $site->slug !!}/cards/{!! $card->id !!}/edit?cardSet={!! $cardset->id !!}"
                               class="btn btn-primary">Edit</a>
                        </div>
                    </div>
                    <br>
                    <p>{!! $card->body !!} </p>
                    <img src="{!! $card->image !!}" alt="Photo">

                </div>
            @endforeach

        </div>
        <br>
    </div>

@stop

@section('scripts')
    <?php
        JavaScript::put([
            'ajaxPath' => Util::ajaxPath('build', 'cardsOrder'),
            'site' => $site,
            'cardsets' => $cardsets,
            'cards' => $cards
        ]);
    ?>

    <script>

        $(function() {
            initializeCardSets();
            createCardList()
            $( "#sortable" )
                    .sortable( {
                        handle: ".handle",

                        update: function (event, ui) {
                            var which = $("#cardset-select").val();
                            var cardset = GBEVars.cardsets[which];
                            var ordinal = 1;
                            var changes = [];
                            $("#sortable li").each(function () {
                                var oldOrdinal = cardset.cardsById[this.id].ordinal;
                                var newOrdinal = ordinal++;
                                if (oldOrdinal != newOrdinal) {
                                    cardset.cardsById[this.id].ordinal = newOrdinal;
                                    var transform = {
                                        "id": parseInt(this.id),
                                        "ord": newOrdinal
                                    };

                                    changes.push(transform);
                                }
                            });
                            if (changes.length > 0) {
                                // Send to the server
                                var source = GBEVars.ajaxPath + "/setOrdinals?changes=" + JSON.stringify(changes);
                                $.get(source, function (r) {
                                }).done(function (r) {
                                    $("#flash").text(r.message);
                                }).fail(function (r) {
                                    $("#flash").text("Error saving order changes : " + r.responseJSON.error.message);
                                });
                                cardset.cards.sort(function (a, b) {
                                    return a.ordinal - b.ordinal;
                                });
                            }
                        }
                    })
                    .selectable({
                        filter: "li",
                        cancel: ".handle",
                        selected: function (event, ui) {
                            var selectedId = null;
                            $(".ui-selected:first", this).each(function() {
                                selectedId = this.id;
                            });
                            var which = $("#cardset-select").val();
                            var cardset = GBEVars.cardsets[which];
                            cardset.currentCard = selectedId;
                            setCardDetail();
                        }
                    })
                    .find( "li")
                    .addClass("ui-corner-all")
            ;
        });

        function setCardDetail ()
        {
            $(".card-detail").each(function () {
                this.style.display = 'none';
            });
            var which = $("#cardset-select").val();
            var cardset = GBEVars.cardsets[which];
            if (cardset.cards.length > 0) {
                var card = cardset.cardsById[cardset.currentCard];
                $("#card_" + card.id).css("display", 'block');
            }
        }
        function initializeCardSets()
        {
            for (var key in GBEVars.cardsets) {
                var cset = GBEVars.cardsets[key];
                if (cset.cards != null) {
                    for (var j=0; j<cset.cards.length; ++j) {
                        cset.cardsById[cset.cards[j].id] = cset.cards[j];
                    }
                }
            }
        }
        function createCardList ()
        {
            var which = $("#cardset-select").val();
            var ulElement = $('#sortable');
            ulElement.empty();
            if (which >= 0) {
                var cardset = GBEVars.cardsets[which];
                cardset.cards.sort(function (a, b) {
                   return a.ordinal - b.ordinal;
                });
                if (cardset.cards.length > 0)
                    cardset.currentCard = cardset.cards[0].id;
                for (var i=0; i<cardset.cards.length; ++i) {
                    var card = cardset.cards[i];
                    var li = '<li id="' + cardset.cards[i].id + '" class="ui-state-default ui-widget-content">' +
                                    '<span class="handle ui-icon ui-icon-arrowthick-2-n-s"></span>' +
                            cardset.cards[i].title + '</li>';
                    ulElement.append(li);
                }
                if (cardset.cards.length > 1) {
                    $("p#list-header").text("Drag items using arrows at left to change their order.");
                }
                else if (cardset.cards.length <= 0) {
                    $("p#list-header").text("No cards have been created for this card set.");
                }
                else {
                    $("p#list-header").text("  \n ");
                }
            }
            setCardDetail();
            $('#sortable li:first').addClass('ui-selected')
        }

        function addCard()
        {
            var which = $("#cardset-select").val();
            window.location.href='/build/' + GBEVars.site.slug + '/cards/create?cardSet='+which;
        }
    </script>
@stop