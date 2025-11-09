<?php
include 'db.php';

// Tambah produk
if(isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $stock = intval($_POST['stock']);
    $price = floatval($_POST['price']);
    $conn->query("INSERT INTO products (name, stock, price) VALUES ('$name', $stock, $price)");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Update produk
if(isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $stock = intval($_POST['stock']);
    $price = floatval($_POST['price']);
    $conn->query("UPDATE products SET name='$name', stock=$stock, price=$price WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Hapus produk
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil data untuk edit
$editData = null;
if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editResult = $conn->query("SELECT * FROM products WHERE id=$id");
    $editData = $editResult->fetch_assoc();
}

// Ambil semua produk
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📦 Stock Produk Kosmetik</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1em;
        }

        .content {
            padding: 40px;
        }

        .form-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            border: 2px solid #e9ecef;
        }

        .form-card h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 8px;
            color: #495057;
            font-weight: 600;
            font-size: 0.9em;
        }

        .form-group input {
            padding: 12px 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 18px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px 18px;
            border-bottom: 1px solid #e9ecef;
        }

        tbody tr {
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-edit, .btn-delete {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-edit {
            background: #ffc107;
            color: #000;
        }

        .btn-edit:hover {
            background: #ffb300;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .price {
            color: #28a745;
            font-weight: 700;
        }

        .stock {
            background: #e7f3ff;
            color: #0066cc;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }

        .stock.low {
            background: #ffe7e7;
            color: #cc0000;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state svg {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.8em;
            }

            table {
                font-size: 0.9em;
            }

            th, td {
                padding: 12px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💄 Stock Produk Kosmetik</h1>
            <p>Kelola inventaris produk kecantikan Anda dengan mudah</p>
        </div>

        <div class="content">
            <!-- Form Tambah/Edit Produk -->
            <div class="form-card">
                <h2><?= $editData ? '✏️ Edit Produk' : '➕ Tambah Produk Baru' ?></h2>
                <form method="POST">
                    <?php if($editData): ?>
                        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="name" placeholder="Contoh: Lipstik Matte" 
                                   value="<?= $editData ? htmlspecialchars($editData['name']) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Stok</label>
                            <input type="number" name="stock" placeholder="0" 
                                   value="<?= $editData ? $editData['stock'] : '' ?>" required min="0">
                        </div>
                        <div class="form-group">
                            <label>Harga (Rp)</label>
                            <input type="number" name="price" placeholder="0" 
                                   value="<?= $editData ? $editData['price'] : '' ?>" required min="0" step="0.01">
                        </div>
                    </div>
                    
                    <?php if($editData): ?>
                        <button type="submit" name="update" class="btn btn-success">💾 Update Produk</button>
                        <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-cancel">❌ Batal</a>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn btn-primary">➕ Tambah Produk</button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabel Produk -->
            <div class="table-container">
                <?php if($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?= $row['id'] ?></strong></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>
                                <span class="stock <?= $row['stock'] < 10 ? 'low' : '' ?>">
                                    <?= $row['stock'] ?> unit
                                </span>
                            </td>
                            <td class="price">Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit=<?= $row['id'] ?>" class="btn-edit">✏️ Edit</a>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn-delete" 
                                       onclick="return confirm('Yakin ingin menghapus produk ini?')">🗑️ Hapus</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3>Belum ada produk</h3>
                    <p>Mulai tambahkan produk kosmetik Anda menggunakan form di atas</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>