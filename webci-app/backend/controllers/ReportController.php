<?php

namespace backend\controllers;

use common\models\Business;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ReportController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $totalBusinesses = Business::find()->count();

        return $this->render('index', [
            'totalBusinesses' => $totalBusinesses,
        ]);
    }

    public function actionExcel()
    {
        $businesses = $this->loadBusinesses();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Aliados');

        $headers = [
            'ID',
            'Nombre',
            'Correo',
            'WhatsApp',
            'Dirección',
            'Mostrar en portada',
            'Categorías',
            'Redes sociales',
            'Plantilla',
            'Fecha creación',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($businesses as $business) {
            $sheet->fromArray([
                $business->id,
                $business->name,
                $business->email,
                $business->whatsapp,
                $business->address,
                $business->show_on_home ? 'Sí' : 'No',
                implode(', ', array_map(static fn($cat) => $cat->name, $business->categories)),
                $business->getSocialLinksString(),
                $business->emailTemplate ? $business->emailTemplate->name : 'Predeterminada',
                Yii::$app->formatter->asDatetime($business->created_at),
            ], null, 'A' . $row);
            $row++;
        }

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return Yii::$app->response->sendContentAsFile(
            $content,
            'aliados-' . date('Ymd-His') . '.xlsx',
            [
                'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    public function actionPdf()
    {
        $businesses = $this->loadBusinesses();

        $pdf = new TCPDF();
        $pdf->SetCreator('WebCI');
        $pdf->SetAuthor('WebCI');
        $pdf->SetTitle('Reporte de Aliados');
        $pdf->SetMargins(12, 20, 12);
        $pdf->AddPage();

        $html = '<h1 style="text-align:center;">Reporte de Aliados</h1>';
        $html .= '<table border="1" cellpadding="6" cellspacing="0" style="font-size:11px;">';
        $html .= '<thead><tr style="background-color:#f1f5f9;">'
            . '<th width="6%">ID</th>'
            . '<th width="17%">Nombre</th>'
            . '<th width="18%">Correo</th>'
            . '<th width="12%">WhatsApp</th>'
            . '<th width="17%">Categorías</th>'
            . '<th width="30%">Redes sociales</th>'
            . '</tr></thead><tbody>';

        foreach ($businesses as $business) {
            $html .= '<tr>'
                . '<td>' . $business->id . '</td>'
                . '<td>' . htmlspecialchars($business->name) . '</td>'
                . '<td>' . htmlspecialchars($business->email) . '</td>'
                . '<td>' . htmlspecialchars($business->whatsapp) . '</td>'
                . '<td>' . htmlspecialchars(implode(', ', array_map(static fn($cat) => $cat->name, $business->categories))) . '</td>'
                . '<td>' . htmlspecialchars($business->getSocialLinksString()) . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        return Yii::$app->response->sendContentAsFile(
            $pdf->Output('aliados-' . date('Ymd-His') . '.pdf', 'S'),
            'aliados-' . date('Ymd-His') . '.pdf',
            [
                'mimeType' => 'application/pdf',
            ]
        );
    }

    /**
     * @return Business[]
     */
    private function loadBusinesses(): array
    {
        return Business::find()
            ->with(['categories', 'emailTemplate'])
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }
}

