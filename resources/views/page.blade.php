@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    {!!
      'Color: '.carbon_get_post_meta( get_the_ID(), 'crb_page_theme' );
    !!}
    <hr/>
    @include('partials.page-header')
    @includeFirst(['partials.content-page', 'partials.content'])
  @endwhile
@endsection
