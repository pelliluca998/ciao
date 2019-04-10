@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Verifica il tuo indirizzo email</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Un nuovo link di verifica è stato inviato al tuo indirizzo email
                        </div>
                    @endif

                    Prima di procedere, è necessario verificare il tuo indirizzo email.<br>
                    Se non hai ricevto il link di verifica, <a href="{{ route('verification.resend') }}">clicca qui per richiederne un altro</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
