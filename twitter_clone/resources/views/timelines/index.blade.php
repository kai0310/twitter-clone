@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 tweets">
                @foreach ($timeline as $tweet)
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <strong>{{ $tweet->user->name }}</strong>
                                -
                                <small>{{ $tweet->created }}</small>
                            </div>
                            <p>{{ $tweet->content }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="">
                                <span class="far fa-comment"></span>
                                {{ $tweet->replies_count ?: null }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="">
                                <span class="fas fa-retweet"></span>
                                {{ $tweet->retweets_count ?: null }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="">
                                <i class="far fa-heart"></i>
                                {{ $tweet->favorites_count ?: null }}
                            </a>
                        </div>
                        <div class="col-md-3">

                        </div>
                    </div>
                    <hr>
                @endforeach
            </div>
        </div>
    </div>
@endsection
