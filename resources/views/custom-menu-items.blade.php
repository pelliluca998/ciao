@foreach($items as $item)
<li @if($item->hasChildren()) class="nav-item dropdown" @else class="nav-item" @endif>

  @if($item->hasChildren())
  <a href="{!! $item->url() !!}" class="dropdown-item dropdown-toggle" data-toggle="dropdown" id="navbarDropdown" role="button" aria-haspopup="true" aria-expanded="false">
    {!! $item->title !!} <span class="caret"></span>
  </a>
  <!-- <div class="dropdown-menu" aria-labelledby="navbarDropdown"> -->
    <ul class="dropdown-menu" role="menu">
      @include('custom-menu-items', array('items' => $item->children()))
    </ul>
  <!-- </div> -->
  @else
  <a class="dropdown-item" href="{!! $item->url() !!}">{!! $item->title !!} </a>
  @endif
</li>
@endforeach
