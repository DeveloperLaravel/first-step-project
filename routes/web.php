<?php

use Illuminate\Support\Facades\Route;
use App\Models\Card;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/cards/{card}/print', function (Card $card) {

    $qr = file_get_contents(storage_path('app/public/images' . $card->qr_path));

    $pdf = Pdf::loadView('pdf.card', [
        'card' => $card,
        'qr' => $qr,
    ]);

    return $pdf->stream("card-{$card->code}.pdf");

})->name('cards.print');
Route::get('/', function () {
    return view('welcome');
});
