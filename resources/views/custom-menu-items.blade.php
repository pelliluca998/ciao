@foreach($items as $item)
  <li @if($item->hasChildren()) class="dropdown" @endif>

      @if($item->hasChildren())
	  <a href="{!! $item->url() !!}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{!! $item->title !!} <span class="caret"></span> </a>
        <ul class="dropdown-menu" role="menu">
              @include('custom-menu-items', array('items' => $item->children()))
        </ul>
	  @else
	  <a href="{!! $item->url() !!}">{!! $item->title !!} </a>
      @endif
  </li>
@endforeach
