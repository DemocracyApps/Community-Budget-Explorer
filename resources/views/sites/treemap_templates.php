<!-- card template -->
<script type="text/html" id="card-template">
    <div class="card {{class}} ">
        <div class="card-img"> </div>
        <div class="card-text">
            <div class="card-value"></div>
            <div class="card-desc"></div>
        </div>
    </div>
</script>

<!-- table template -->
<script type="text/html" id="table-template">
    <div id="table-container" >
        <div class="tablerow" id="table-header" > <div class="bullet"> </div>
        </div>
</script>

<!-- table header template -->
<script type="text/html" id="table-header-template">
    <div class="tablerow" id="table-header" data-level=0>
        <div class="bullet"> </div>
        {{#.}}
        <div class="{{cellClass}} head"> {{title}} </div>
        {{/.}}
    </div>
</script>

<!-- table row template -->
<script type="text/html" id="row-template">
    <div class="tablerow">
        <div class="bullet"> <img class="expand-icon" src="/img/listBullet.png" /> </div>
    </div>
</script>

<!-- year dropdown template -->
<script type="text/html" id="dropdown-template">
    <li role="presentation">
        <a role="menuitem" tabindex="-1" href="#">{{.}}</a>
    </li>
</script>
