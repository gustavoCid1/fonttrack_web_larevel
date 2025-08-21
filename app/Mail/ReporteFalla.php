<?php

/**
 * Clase ReporteFalla - Envío de correos con reportes de fallas
 * 
 * Mailable de Laravel para enviar correos electrónicos con reportes
 * de fallas adjuntos en formato PDF. Prepara los datos del reporte,
 * carga la información del lugar y materiales, y configura el correo
 * con el PDF adjunto para notificación automática.
 * 
 * @author Gustavo angel cid flores 
 * @version 2.0.0
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Lugar;

class ReporteFalla extends Mailable
{
    use Queueable, SerializesModels;

    public $falla;
    public $pdfContent;
    public $data;

    /**
     * Crear nueva instancia del mensaje de correo
     * 
     * Recibe los datos de la falla y el contenido del PDF,
     * luego prepara toda la información necesaria para el correo
     * incluyendo datos del lugar y materiales formateados.
     *
     * @param object $falla Objeto con datos de la falla
     * @param string $pdfContent Contenido binario del PDF
     * @return void
     */
    public function __construct($falla, $pdfContent)
    {
        $this->falla = $falla;
        $this->pdfContent = $pdfContent;

        // Preparar los datos para la vista del email
        $lugar = Lugar::find($falla->id_lugar);
        $nombreLugar = $lugar ? $lugar->nombre : 'No especificado';

        $materials = [];
        if ($falla->materials) {
            $materials = is_string($falla->materials) ? json_decode($falla->materials, true) : $falla->materials;
            $materials = is_array($materials) ? $materials : [];
        }

        // Preparar datos formateados para la vista del correo
        $this->data = [
            'id_reporte' => $falla->id,
            'lugar' => $nombreLugar,
            'eco' => $falla->eco ?? 'N/A',
            'placas' => $falla->placas ?? 'N/A',
            'marca' => $falla->marca ?? 'N/A',
            'anio' => $falla->anio ?? 'N/A',
            'km' => $falla->km ?? 'N/A',
            'fecha' => $falla->fecha ?? 'N/A',
            'nombre_conductor' => $falla->nombre_conductor ?? 'N/A',
            'descripcion' => $falla->descripcion ?? 'N/A',
            'observaciones' => $falla->observaciones ?? 'N/A',
            'autorizado_por' => $falla->autorizado_por ?? 'N/A',
            'reviso_por' => $falla->reviso_por ?? $falla->autorizado_por ?? 'N/A',
            'materials' => $materials,
        ];
    }

    /**
     * Construir el mensaje de correo electrónico
     * 
     * Configura el asunto, vista del correo, datos a pasar
     * y adjunta el PDF del reporte con nombre descriptivo.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reporte de Falla #' . $this->falla->id . ' - FontTrack')
            ->view('emails.reporte_falla')
            ->with([
                'data' => $this->data,
                'falla' => $this->falla
            ])
            ->attachData($this->pdfContent, 'reporte_falla_' . $this->falla->id . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}