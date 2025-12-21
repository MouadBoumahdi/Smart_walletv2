<?php
session_start();

include "cards/afficher_card.php";

$cards = [];
while($row = mysqli_fetch_assoc($result)) {
    $cards[] = $row;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}



// depense recurrente
$depense_recurrente_query = "SELECT * from transaction where user_id = " . $_SESSION['user_id'] . " and type='depense' and MONTH(created_at) = MONTH(CURRENT_DATE()) and YEAR(created_at) = YEAR(CURRENT_DATE()) GROUP BY description having count(*) > 1";
$depense_recurrente_result = mysqli_query($connect, $depense_recurrente_query);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire Financier - Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding-bottom: 40px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo h1 {
            font-size: 28px;
            font-weight: 700;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info span {
            font-size: 18px;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logout-btn:hover {
            background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(244, 67, 54, 0.3);
        }
        
        .dashboard-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        @media (min-width: 1200px) {
            .dashboard-content {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }
        
        section {
            background-color: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            height: fit-content;
        }
        
        section:hover {
            transform: translateY(-5px);
        }
        
        section h2 {
            color: #1a237e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8eaf6;
            font-size: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .card-wrapper {
            display: flex;
            flex-direction: column;
        }
        
        .credit-card {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: white;
            border-radius: 16px;
            padding: 20px;
            position: relative;
            box-shadow: 0 10px 20px rgba(26, 35, 126, 0.2);
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .card-type {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .card-chip {
            width: 45px;
            height: 35px;
            background: linear-gradient(135deg, #ffd54f 0%, #ffb300 100%);
            border-radius: 8px;
            position: relative;
        }
        
        .card-chip:after {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
        }
        
        .card-number {
            font-size: 20px;
            letter-spacing: 2px;
            text-align: center;
            margin: 12px 0;
            font-family: 'Courier New', monospace;
        }
        
        .card-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .card-holder {
            display: flex;
            flex-direction: column;
        }
        
        .card-label {
            font-size: 11px;
            opacity: 0.8;
            margin-bottom: 5px;
        }
        
        .card-expiry {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .card-balance {
            background-color: white;
            border-radius: 0 0 12px 12px;
            padding: 15px;
            margin-top: -5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .balance-label {
            font-size: 14px;
            color: #666;
            font-weight: 600;
        }
        
        .balance-amount {
            font-size: 22px;
            font-weight: 700;
            color: #1a237e;
        }
        
        .card-cih .credit-card {
            background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);
        }
        
        .add-card-btn {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .add-card-btn:hover {
            background-color: #388e3c;
            transform: translateY(-3px);
        }
        
        .transaction-btn {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            margin-bottom: 20px;
        }
        
        .transaction-btn:hover {
            background: linear-gradient(135deg, #3949ab 0%, #1a237e 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(57, 73, 171, 0.3);
        }
        
        .transfer-btn {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            margin-bottom: 20px;
        }
        
        .transfer-btn:hover {
            background: linear-gradient(135deg, #f57c00 0%, #ff9800 100%);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(255, 152, 0, 0.3);
        }
        
        .info-section {
            background-color: #e8f5e9;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            text-align: center;
        }
        
        .info-section p {
            color: #2e7d32;
            margin: 0;
            font-size: 15px;
        }
        
        .info-icon {
            margin-right: 10px;
            color: #4caf50;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        
        .form-group label {
            margin-bottom: 8px;
            color: #1a237e;
            font-weight: 600;
            font-size: 14px;
        }
        
        input, select, textarea {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3949ab;
            box-shadow: 0 0 0 3px rgba(57, 73, 171, 0.2);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            width: 100%;
        }
        
        .submit-btn:hover {
            background: linear-gradient(135deg, #3949ab 0%, #1a237e 100%);
            transform: translateY(-3px);
        }
        
        .dynamic-field {
            display: none;
        }
        
        .dynamic-field.active {
            display: flex;
            flex-direction: column;
        }
        
        .card-field {
            display: flex;
            flex-direction: column;
        }
        
        .recurring-list {
            margin-top: 10px;
        }
        
        .recurring-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            background-color: #f9f9f9;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        
        .recurring-info {
            flex-grow: 1;
        }
        
        .recurring-info h4 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .recurring-details {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #666;
        }
        
        .recurring-amount {
            font-size: 18px;
            font-weight: 700;
            color: #f44336;
            min-width: 80px;
            text-align: right;
        }
        
        .add-recurring-btn {
            background-color: #2196f3;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        
        .add-recurring-btn:hover {
            background-color: #1976d2;
            transform: translateY(-3px);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 16px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e8eaf6;
        }
        
        .modal-header h3 {
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .close-modal:hover {
            color: #f44336;
        }
        
        .modal-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .modal-submit-btn {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            width: 100%;
        }
        
        .modal-submit-btn:hover {
            background: linear-gradient(135deg, #3949ab 0%, #1a237e 100%);
            transform: translateY(-3px);
        }
        
        .transaction-modal .modal-header h3 {
            color: #1a237e;
        }
        
        .transfer-modal .modal-header h3 {
            color: #ff9800;
        }
        
        footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        
        /* Transaction History Table Styles */
        .transaction-history-section {
            background-color: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            margin-top: 30px;
            grid-column: 1 / -1;
        }
        
        .transaction-history-section h2 {
            color: #1a237e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8eaf6;
            font-size: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e8eaf6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        
        .transaction-table thead {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            border-bottom: 2px solid #3949ab;
        }
        
        .transaction-table th {
            padding: 16px 15px;
            text-align: left;
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .transaction-table th:last-child {
            border-right: none;
        }
        
        .transaction-table tbody tr {
            border-bottom: 1px solid #e8eaf6;
            transition: background-color 0.3s ease;
        }
        
        .transaction-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .transaction-table tbody tr:hover {
            background-color: #e8eaf6;
        }
        
        .transaction-table td {
            padding: 14px 15px;
            color: #333;
            font-size: 14px;
            border-right: 1px solid #e8eaf6;
        }
        
        .transaction-table td:last-child {
            border-right: none;
        }
        
        .transaction-type {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .transaction-type.revenue {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .transaction-type.depense {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .amount-cell {
            font-weight: 600;
            color: #1a237e;
        }
        
        .no-data {
            text-align: center;
            padding: 40px !important;
            color: #666;
            font-style: italic;
            background-color: #f8f9fa !important;
        }
        
        @media (max-width: 767px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .dashboard-content {
                grid-template-columns: 1fr;
            }
            
            section {
                padding: 20px;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                padding: 20px;
                width: 95%;
            }
            
            .transaction-table th,
            .transaction-table td {
                padding: 12px 10px;
                font-size: 13px;
            }
            
            .transaction-history-section {
                padding: 20px;
            }
            
            .transaction-history-section h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-chart-line fa-2x"></i>
                    <h1>Gestionnaire Financier</h1>
                </div>
                <div class="user-info">
                    <span>Bonjour, <?php echo $_SESSION['name']; ?></span>
                    <form method="POST" action="auth/logout.php">
                        <button class="logout-btn" name="logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    
    <main class="container">
        <div class="dashboard-content">
            <section class="cards-section">
                <h2>Mes Cartes <i class="fas fa-credit-card"></i></h2>
                
                <div class="cards-container">
                    <?php
                        foreach($cards as $row){
                            echo '
                                <div class="card-wrapper">
                                    <div class="credit-card">
                                        <div class="card-header">
                                            <div class="card-type">'.$row['bank_name'].'</div>
                                            <div class="card-chip"></div>
                                        </div>
                                        <div class="card-number">****  ****  ****  '.substr($row['card_number'],-4).'</div>
                                        <div class="card-details">
                                            <div class="card-holder">
                                                <div class="card-label">Titulaire</div>
                                                <div class="card-name">'.$row['card_name'].'</div>
                                            </div>
                                            <div class="card-expiry">
                                                <div class="card-label">Valide jusqu\'au</div>
                                                <div class="expiry-date">'.$row['card_expiration'].'</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-balance">
                                        <span class="balance-label">Solde actuel</span>
                                        <span class="balance-amount">'.$row['balance'].' DH</span>
                                    </div>
                                </div>
                            ';
                        }
                     ?>
                </div>
                
                <button class="add-card-btn" onclick="openModal('addCardModal')">
                    <i class="fas fa-plus"></i> Ajouter une nouvelle carte
                </button>
            </section>
            
            <section class="transaction-section">
                <h2>Opérations Financières <i class="fas fa-exchange-alt"></i></h2>
                
                <button type="button" class="transaction-btn" onclick="openModal('transactionModal')">
                    <i class="fas fa-plus-circle"></i> Ajouter une Transaction
                </button>
                
                <button type="button" class="transfer-btn" onclick="openModal('transferModal')">
                    <i class="fas fa-paper-plane"></i> Envoyer à un autre utilisateur
                </button>
                
                <div class="info-section">
                    <p><i class="fas fa-info-circle info-icon"></i> 
                       <strong>Choisissez une option pour ajouter une transaction ou envoyer de l'argent</strong>
                    </p>
                </div>
            </section>
            
            <section class="recurring-section">
                <h2>Dépenses récurrentes <i class="fas fa-redo-alt"></i></h2>
                
                <div class="recurring-list" id="recurringList">
                        <?php
                            while($row = mysqli_fetch_assoc($depense_recurrente_result)){
                                echo '
                                    <div class="recurring-item">
                                        <div class="recurring-info">
                                            <h4>'.$row['description'].'</h4>
                                            <div class="recurring-details">
                                                <span>Catégorie: '.$row['description'].'</span>
                                            </div>
                                        </div>
                                        <div class="recurring-amount">
                                            '.$row['amount'].' DH
                                        </div>
                                    </div>
                                ';
                            }
                        ?>
                </div>
            
            </section>
            
            <!-- Transaction History Section -->
            <section class="transaction-history-section">
                <h2>Historique des Transactions <i class="fas fa-history"></i></h2>
                
                <div class="table-container">
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Montant (DH)</th>
                                <th>Carte</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                
                                $transaction_query = "SELECT t.created_at, t.type, t.amount, c.bank_name, t.description 
                                                     FROM transaction t 
                                                     JOIN cards c ON t.card_id = c.id 
                                                     WHERE c.user_id = $user_id 
                                                     ORDER BY t.created_at DESC";

                                $sendmoney_query = "SELECT s.created_at, s.type,s.person_id , s.amount, c.bank_name 
                                                    FROM sendmoney s
                                                    JOIN cards c ON s.card_id = c.id
                                                    WHERE c.user_id = $user_id
                                                    ORDER BY s.created_at DESC";

                                $result_transaction = mysqli_query($connect, $transaction_query);
                                $result_sendmoney = mysqli_query($connect, $sendmoney_query);
                                
                                $transaction = [];
                                
                                if(mysqli_num_rows($result_transaction) > 0) {
                                    while($row = mysqli_fetch_assoc($result_transaction)) {
                                        $transaction[] = $row;
                                    }
                                }

                                if(mysqli_num_rows($result_sendmoney) > 0) {
                                    while($row = mysqli_fetch_assoc($result_sendmoney)) {
                                        $transaction[] = $row;
                                    }
                                }

                                    foreach($transaction as $row) {
                                        echo "<tr>";
                                        echo "<td>" . date('d/m/Y H:i', strtotime($row['created_at'])) . "</td>";
                                        echo "<td><span class='transaction-type'>" . ucfirst($row['type']) . "</span></td>";
                                        echo "<td class='amount-cell'>" . $row['amount'] . " DH</td>";
                                        echo "<td>" . $row['bank_name'] . "</td>";
                                        echo "<td>" . ($row['description'] ?? 'Send to person number ' . $row['person_id']) . "</td>";
                                        echo "</tr>";
                                    }
                                
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
    
    <div class="modal" id="addCardModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-credit-card"></i> Ajouter une nouvelle carte</h3>
                <button class="close-modal" onclick="closeModal('addCardModal')">&times;</button>
            </div>
            <form class="modal-form" id="addCardForm" action="cards/add_card.php" method="POST">
                <div class="form-group">
                    <label for="cardBank">Banque</label>
                    <select id="cardBank" name="cardBank" required>
                        <option value="">Sélectionnez la banque</option>
                        <option value="cih">CIH Bank</option>
                        <option value="bp">BMCE</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="cardHolder">Nom du titulaire</label>
                    <input type="text" id="cardHolder" placeholder="Nom complet" name="cardHolder" required>
                </div>

                <div class="form-group">
                    <label for="cardNumber">Numéro de carte</label>
                    <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" name="cardNumber" required>
                </div>
                
                <div class="form-group">
                    <label for="cardExpiry">Date d'expiration</label>
                    <input type="text" id="cardExpiry" placeholder="MM/AA" name="cardExpiry" required>
                </div>
                
                <div class="form-group">
                    <label for="cardCVV">Code de sécurité (CVV)</label>
                    <input type="password" id="cardCVV" placeholder="123" name="cardCVV" required>
                </div>
                
                <div class="form-group">
                    <label for="initialBalance">Solde initial (DH)</label>
                    <input type="number" id="initialBalance" placeholder="0" name="initialBalance" value="0">
                </div>

                <div class="form-group">
                    <label for="typecard">Type de carte</label>
                    <select id="typecard" name="typecard" required>
                        <option value="">Sélectionnez le type</option>
                        <option value="Primary">Principale</option>
                        <option value="Secondaire">Secondaire</option>
                    </select>
                </div>
                
                <button type="submit" class="modal-submit-btn">
                    <i class="fas fa-save"></i> Ajouter la carte
                </button>
            </form>
        </div>
    </div>
    
    <div class="modal transaction-modal" id="transactionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exchange-alt"></i> Nouvelle Transaction</h3>
                <button class="close-modal" onclick="closeModal('transactionModal')">&times;</button>
            </div>
            
            <form class="modal-form" id="transactionForm" action="transaction/add_transaction.php" method="POST">
                <div class="form-group">
                    <label for="transactionType">Type de transaction*</label>
                    <select id="transactionType" name="type" required onchange="toggleFields()">
                        <option value="">Sélectionnez le type</option>
                        <option value="revenue">Revenu</option>
                        <option value="depense">Dépense</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="transactionAmount">Montant (DH)*</label>
                    <input type="number" id="transactionAmount" name="amount" 
                           placeholder="Entrez le montant" required step="0.01" min="0.01">
                </div>
                
                <div class="form-group dynamic-field" id="sourceField">
                    <label for="transactionSource">Source du revenu*</label>
                    <select id="transactionSource" name="source">
                        <option value="">Sélectionnez la source</option>
                        <option value="Salaire">Salaire</option>
                        <option value="Freelance">Freelance</option>
                        <option value="Investissement">Investissement</option>
                        <option value="Cadeau">Cadeau</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                
                <div class="form-group dynamic-field" id="categoryField">
                    <label for="transactionCategory">Catégorie*</label>
                    <select id="transactionCategory" name="category">
                        <option value="">Sélectionnez la catégorie</option>
                        <option value="Nourriture">Nourriture</option>
                        <option value="Transport">Transport</option>
                        <option value="Santé">Santé</option>
                    </select>
                </div>
                
                <div class="form-group card-field" id="cardField">
                    <label for="transactionCard">Carte*</label>
                    <select id="transactionCard" name="card_id">
                        <option value="">Sélectionnez la carte</option>
                        <?php
                            foreach($cards as $row){
                                $type_text = ($row['type'] == 'Primary') ? ' (Principale)' : '';
                                echo '<option value="'.$row['id'].'">'
                                    .$row['bank_name'].' - ****'.substr($row['card_number'],-4)
                                    .$type_text.' - '.$row['balance'].' DH'
                                    .'</option>';
                            }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="modal-submit-btn">
                    <i class="fas fa-save"></i> Enregistrer la transaction
                </button>
            </form>
        </div>
    </div>
    
    <div class="modal transfer-modal" id="transferModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-paper-plane"></i> Envoyer de l'argent</h3>
                <button class="close-modal" onclick="closeModal('transferModal')">&times;</button>
            </div>
            
            <form class="modal-form" id="transferForm" action="transaction/send_money.php" method="POST">
                <div class="form-group">
                    <label for="transferEmail">Email du destinataire*</label>
                    <input type="number" id="transferEmail" name="id" 
                           placeholder="ID: 1" required>
                </div>
                
                <div class="form-group">
                    <label for="transferAmount">Montant (DH)*</label>
                    <input type="number" id="transferAmount" name="amount" 
                           placeholder="Entrez le montant" required step="0.01" min="0.01">
                </div>
                
                <div class="form-group">
                    <label for="transferCard">Carte à utiliser*</label>
                    <select id="transferCard" name="card_id" required>
                        <option value="">Sélectionnez la carte</option>
                        <?php
                            foreach($cards as $row){
                                echo '<option value="'.$row['id'].'">'
                                    .$row['bank_name'].' - ****'.substr($row['card_number'],-4)
                                    .' (Solde: '.$row['balance'].' DH)'
                                    .'</option>';
                            }
                        ?>
                    </select>
                </div>
                
                <button type="submit" class="modal-submit-btn">
                    <i class="fas fa-paper-plane"></i> Envoyer l'argent
                </button>
            </form>
        </div>
    </div>
    
    

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        function toggleFields() {
            const type = document.getElementById('transactionType').value;
            const sourceField = document.getElementById('sourceField');
            const categoryField = document.getElementById('categoryField');
            const cardField = document.getElementById('cardField');
            
            sourceField.classList.remove('active');
            categoryField.classList.remove('active');
            cardField.style.display = 'none';
            
            document.getElementById('transactionSource').removeAttribute('required');
            document.getElementById('transactionCategory').removeAttribute('required');
            document.getElementById('transactionCard').removeAttribute('required');
            
            if (type === 'revenue') {
                sourceField.classList.add('active');
                document.getElementById('transactionSource').setAttribute('required', 'required');
                
            } else if (type === 'depense') {
                categoryField.classList.add('active');
                cardField.style.display = 'flex';
                cardField.style.flexDirection = 'column';
                
                document.getElementById('transactionCategory').setAttribute('required', 'required');
                document.getElementById('transactionCard').setAttribute('required', 'required');
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
            
            document.getElementById('transactionForm').addEventListener('submit', function(e) {
                const amount = document.getElementById('transactionAmount').value;
                if (!amount || amount <= 0) {
                    e.preventDefault();
                    alert('Veuillez entrer un montant valide');
                    return false;
                }
                return true;
            });
            
            document.getElementById('transferForm').addEventListener('submit', function(e) {
                const amount = document.getElementById('transferAmount').value;
                const email = document.getElementById('transferEmail').value;
                
                if (!amount || amount <= 0) {
                    e.preventDefault();
                    alert('Veuillez entrer un montant valide');
                    return false;
                }
                
                if (!email) {
                    e.preventDefault();
                    alert('Veuillez entrer un email valide');
                    return false;
                }
                
                return confirm('Confirmer l\'envoi de ' + amount + ' DH ?');
            });
        });
    </script>
</body>
</html>