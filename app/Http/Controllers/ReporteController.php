<?php

/**
 * Controlador Reporte - Envío de reportes por correo electrónico
 * 
 * Maneja la generación de PDFs y envío de reportes de fallas por correo.
 * Incluye validaciones de datos, generación temporal de archivos PDF,
 * envío de correos con adjuntos y limpieza automática de archivos temporales.
 * 
 * @author Gustavo Angel Cid Flores
 * @author Daniela Pérez Peralta
 * @version 2.0.0
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReporteController extends Controller
{
    /**
     * Enviar reporte de falla por correo electrónico con PDF adjunto
     */
    public function enviar(Request $request)
    {
        $data = $request->all();

        // Validar datos requeridos para el reporte
        $data = $request->validate([
            'correo' => 'required|email',
            'asunto' => 'required|string',
            'descripcion' => 'required|string',
            'lugar' => 'nullable|string',
        ]);

        // Generar el PDF desde la vista de reporte
        $pdf = Pdf::loadView('reporte-fallo-pdf', compact('data'));

        // Guardar PDF temporalmente en storage
        $pdfPath = storage_path('app/public/reporte_fallo.pdf');
        $pdf->save($pdfPath);

        // Enviar correo con el PDF adjunto
        Mail::send('emails.reporte_enviado', ['data' => $data], function ($message) use ($data, $pdfPath) {
            $message->to($data['correo'])
                ->subject('Reporte de Falla Recibido')
                ->attach($pdfPath);
        });

        // Eliminar PDF temporal después del envío
        Storage::delete('public/reporte_fallo.pdf');

        return back()->with('success', 'Reporte enviado correctamente por correo.');
    }
}