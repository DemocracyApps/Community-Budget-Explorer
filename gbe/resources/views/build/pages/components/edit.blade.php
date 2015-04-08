@extends('templates.default')

@section('content')

    <form method="POST" action="/build/{!! $site->slug !!}/pages/{!! $page->id !!}/components/{!! $pageComponent->id !!}" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="_method" value="PUT">

        <h1>Configure Component</h1>

        @foreach ($dataDefs as $def)

            <?php
                $dName = $def['name'];
                $dType = $def['type'];
                $dTag = $def['tag'];
                $dDescription = $def['description'];
                reset($cardSets);
                $firstSet = key($cardSets);
            ?>
            <div class="row">
                <div class="col-xs-12">
                    <h3>{!! $dName !!}</h3>
                    <p>{!! $dDescription !!}</p>
                </div>

                @if ($dType == 'card' || $dType == 'cardset')
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="selectedSet_{!! $dTag !!}">CardSet:</label>
                            <select id="selectedSet_{!! $dTag !!}" class="form-control" name="selectedSet_{!! $dTag !!}"
                                    @if ($dType == 'card')
                                        onchange='return selectCardSet("{!! $dTag !!}")'
                                    @endif
                                    >
                                @foreach ($cardSets as $set)
                                    <option value="{!! $set->id !!}">
                                        {!! $set->name !!}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if ($dType == 'card')
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="selectedCard_{!! $dTag !!}">Card:</label>
                                <select class="form-control" id="selectedCard_{!! $dTag !!}" name="selectedCard_{!! $dTag !!}">
                                    @foreach($cardSets[$firstSet]->cards as $card)
                                        <option value="{!! $card->id !!}">{!! $card->title !!} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                @elseif ($dType == 'dataset' || $dType == 'dataset_list')

                @else
                    <br>
                    <p class="error text-danger bg-danger">Unknown data type</p>
                @endif
            </div>
        @endforeach

        <div class="form-group">
            {!!  Form::submit('Save', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop


@section('scripts')
    <?php
    JavaScript::put([
            'ajaxPath' => Util::ajaxPath('build', 'components'),
            'site' => $site,
            'page' => $page->id,
            'cardSets'=>$cardSets
    ]);
    ?>

    <script>
        $(function() {

        });

        function selectCardSet(tag) {
            var value = $("#set_"+tag).val();
            var cardSet = GBEVars.cardSets[value];
            var cardSelect = $("#card_"+tag);
            cardSelect.empty();
            for (var i=0; i<cardSet.cards.length; ++i) {
                var card = cardSet.cards[i];
                var option = '<option value="'+ card.id + '">'+card.title+'</option>';
                cardSelect.append(option);
            }
        }
    </script>
@stop
