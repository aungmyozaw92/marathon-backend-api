{{-- @extends('errors::illustrated-layout')

@section('code', '404')
@section('title', __('Page Not Found'))

@section('image')
    <div style="background-image: url({{ asset('/svg/404.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@section('message', __('Sorry, the page you are looking for could not be found.')) --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <style>
        img{
            width: 40%;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div>
        <img src="{{ asset('/svg/404error.svg') }}" alt="">
    </div>
</body>
</html>