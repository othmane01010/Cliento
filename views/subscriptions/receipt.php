<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu d'adhésion #<?= (int) $subscription['id'] ?> - <?= htmlspecialchars($subscription['client_name']) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #eef2f5; padding: 30px 15px; color: #2c3e50; }
        
        .receipt-container { max-width: 650px; margin: 0 auto; background: #fff; border-radius: 12px; border: 1px solid #dcdfe6; padding: 35px; box-shadow: 0 5px 20px rgba(0,0,0,0.06); }
        .receipt-header { text-align: center; border-bottom: 2px dashed #e2e8f0; padding-bottom: 20px; margin-bottom: 25px; }
        .receipt-header h1 { font-size: 24px; color: #1e293b; text-transform: uppercase; letter-spacing: 1px; }
        .receipt-header p { font-size: 13px; color: #64748b; margin-top: 5px; }

        .client-box { display: flex; align-items: center; gap: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom: 25px; }
        .client-avatar { width: 75px; height: 75px; border-radius: 50%; object-fit: cover; border: 3px solid #3b82f6; }
        .client-meta h3 { margin: 0 0 4px; font-size: 18px; color: #0f172a; }
        .client-meta span { display: block; font-size: 13px; color: #64748b; margin-top: 2px; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .info-table th, .info-table td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .info-table th { color: #64748b; width: 45%; font-weight: 600; }
        .info-table td { color: #0f172a; font-weight: 600; text-align: right; }

        .total-row { background: #f0fdf4; border-radius: 8px; }
        .total-row th, .total-row td { color: #166534; font-size: 16px; border: none; padding: 15px 10px; }

        .receipt-footer { text-align: center; border-top: 2px dashed #e2e8f0; padding-top: 20px; font-size: 12px; color: #94a3b8; }
        
        .actions-bar { max-width: 650px; margin: 0 auto 20px; display: flex; justify-content: space-between; align-items: center; }
        .btn-print { background: #2563eb; color: #fff; border: none; padding: 10px 22px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back { color: #64748b; text-decoration: none; font-size: 14px; font-weight: 500; }
        .btn-back:hover { color: #0f172a; }

        @media print {
            body { background: #fff; padding: 0; }
            .actions-bar { display: none; }
            .receipt-container { box-shadow: none; border: 1px solid #cbd5e1; max-width: 100%; padding: 25px; }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <a href="/subscriptions/show/<?= (int) $subscription['id'] ?>" class="btn-back">← Retour aux détails</a>
        <button onclick="window.print()" class="btn-print">🖨️ Imprimer le reçu (PDF)</button>
    </div>

    <div class="receipt-container">
        <div class="receipt-header">
            <h1>FICHE D'ADHÉSION & REÇU</h1>
            <p>Reçu officiel N° #<?= str_pad((string)$subscription['id'], 5, '0', STR_PAD_LEFT) ?> • Émis le <?= date('d/m/Y à H:i') ?></p>
        </div>

        <div class="client-box">
            <img 
                src="/uploads/clients/<?= htmlspecialchars($subscription['client_photo'] ?? 'default.png') ?>" 
                alt="Photo" 
                class="client-avatar"
            >
            <div class="client-meta">
                <h3><?= htmlspecialchars($subscription['client_name']) ?></h3>
                <span><strong>CIN :</strong> <?= !empty($subscription['client_cin']) ? htmlspecialchars($subscription['client_cin']) : 'Non renseigné' ?></span>
                <span><strong>Téléphone :</strong> <?= htmlspecialchars($subscription['client_phone']) ?></span>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <th>Formule souscrite</th>
                <td><?= htmlspecialchars($subscription['plan_name']) ?></td>
            </tr>
            <tr>
                <th>Durée de l'abonnement</th>
                <td><?= (int) ($subscription['plan_duration'] ?? 0) ?> Jours</td>
            </tr>
            <tr>
                <th>Date de Début</th>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($subscription['start_date']))) ?></td>
            </tr>
            <tr>
                <th>Date d'Expiration</th>
                <td style="color: #dc2626;"><?= htmlspecialchars(date('d/m/Y', strtotime($subscription['end_date']))) ?></td>
            </tr>
            <tr class="total-row">
                <th>Montant Total Réglé</th>
                <td><?= number_format($subscription['plan_price'], 2) ?> DH</td>
            </tr>
        </table>

        <div class="receipt-footer">
            <p>Ce document atteste de votre adhésion valide aux services pour la période indiquée.</p>
            <p style="margin-top: 5px;">Système de gestion <strong>Cliento</strong></p>
        </div>
    </div>

</body>
</html>