<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Exception untuk error operasional shipping API (license expired, koneksi gagal, dll).
 *
 * Dibedakan dari Throwable umum supaya controller bisa handle spesifik:
 * - Logging sudah dilakukan di layer bawah (AgenwebsiteClient)
 * - Controller convert ke pesan user-friendly
 * - No-coverage asli (success tapi empty) TIDAK throw ini
 */
class ShippingRateException extends RuntimeException
{
    //
}
