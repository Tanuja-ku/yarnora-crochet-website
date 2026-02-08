<?php
session_start();
require_once "config.php";
require_once "products.php";

/* ===== ADMIN CHECK ===== */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT full_name, is_admin FROM users WHERE id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || $user['is_admin'] != 1) {
    echo "Access Denied";
    exit;
}

$page = $_GET['page'] ?? "dashboard";

/* ===== HANDLE POST ACTIONS ===== */
if ($_SERVER['REQUEST_METHOD']=="POST") {

    if(isset($_POST['update_order_status'])){
        $id=$_POST['order_id'];
        $status=$_POST['status'];
        $stmt=$conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si",$status,$id);
        $stmt->execute();
    }

    if(isset($_POST['update_custom_status'])){
        $id=$_POST['custom_id'];
        $status=$_POST['status'];
        $stmt=$conn->prepare("UPDATE custom_orders SET status=? WHERE id=?");
        $stmt->bind_param("si",$status,$id);
        $stmt->execute();
    }

    if(isset($_POST['delete_review'])){
        $id=$_POST['review_id'];
        $stmt=$conn->prepare("DELETE FROM reviews WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }

    header("Location: admin.php?page=$page");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Panel | Yarnora</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f8f9fa;font-family:Poppins;}
.sidebar{background:#fff;height:100vh;padding:20px;}
.sidebar a{display:block;padding:10px;border-radius:8px;color:#333;text-decoration:none;margin-bottom:5px;}
.sidebar a.active,.sidebar a:hover{background:#ff9eb5;color:#fff;}
.card{border:none;box-shadow:0 4px 10px rgba(0,0,0,.05);}
</style>
</head>

<body>

    <nav class="navbar navbar-light bg-white shadow-sm px-3 d-flex justify-content-between">
    <span class="navbar-brand fw-bold text-primary">Yarnora Admin</span>

    <div>
        <span class="me-3">Welcome <?= htmlspecialchars($check['full_name']) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
    </div>
</nav>


<div class="container-fluid">
<div class="row">

<div class="col-md-2 sidebar">
    <a href="admin.php" class="<?= $page=='dashboard'?'active':'' ?>">Dashboard</a>
    <a href="admin.php?page=orders" class="<?= $page=='orders'?'active':'' ?>">Orders</a>
    <a href="admin.php?page=custom" class="<?= $page=='custom'?'active':'' ?>">Custom Orders</a>
    <a href="admin.php?page=users" class="<?= $page=='users'?'active':'' ?>">Users</a>
    <a href="admin.php?page=reviews" class="<?= $page=='reviews'?'active':'' ?>">Reviews</a>
    <a href="admin.php?page=messages" class="<?= $page=='messages'?'active':'' ?>">Messages</a>
    <a href="admin.php?page=products" class="<?= $page=='products'?'active':'' ?>">Products</a>
</div>

<div class="col-md-10 p-4">

<?php if($page=="dashboard"): ?>

<h2>Dashboard</h2>
<?php
$users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$revenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status='Completed'")->fetch_row()[0];
?>
<div class="row g-3">
<div class="col-md-4"><div class="card p-3">Users: <?= $users ?></div></div>
<div class="col-md-4"><div class="card p-3">Orders: <?= $orders ?></div></div>
<div class="col-md-4"><div class="card p-3">Revenue: ₹<?= $revenue ?></div></div>
</div>

   <?php elseif($page=="orders"): ?>

<h2>Orders</h2>

<table class="table table-bordered table-hover align-middle">
<thead class="table-light">
<tr>
    <th>Order ID</th>
    <th>User</th>
    <th>Products</th>
    <th>Total</th>
    <th>Status</th>
    <th>Update</th>
</tr>
</thead>

<tbody>
<?php
$sql = "
SELECT o.id,o.total_amount,o.status,u.full_name,
       oi.product_id,oi.quantity
FROM orders o
JOIN users u ON o.user_id=u.id
JOIN order_items oi ON o.id=oi.order_id
ORDER BY o.created_at DESC
";

$res = $conn->query($sql);

$orders = [];

while($row = $res->fetch_assoc()){
    $orders[$row['id']]['user'] = $row['full_name'];
    $orders[$row['id']]['total'] = $row['total_amount'];
    $orders[$row['id']]['status'] = $row['status'];
    $orders[$row['id']]['items'][] = [
        'product_id' => $row['product_id'],
        'quantity' => $row['quantity']
    ];
}
?>

<?php foreach($orders as $order_id => $data): ?>
<tr>
    <td>#<?= $order_id ?></td>
    <td><?= htmlspecialchars($data['user']) ?></td>

    <!-- PRODUCTS -->
    <td>
        <?php foreach($data['items'] as $item): 
            $productName = "Product #".$item['product_id'];
            foreach($products as $p){
                if($p['id']==$item['product_id']){
                    $productName = $p['name'];
                    break;
                }
            }
        ?>
            <div>• <?= htmlspecialchars($productName) ?> (Qty: <?= $item['quantity'] ?>)</div>
        <?php endforeach; ?>
    </td>

    <td>₹<?= number_format($data['total'],2) ?></td>

    <td>
        <span class="badge bg-info"><?= htmlspecialchars($data['status']) ?></span>
    </td>

    <td>
        <form method="post" class="d-flex gap-2">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">

            <select name="status" class="form-select form-select-sm">
                <option value="Pending" <?= $data['status']=="Pending"?'selected':'' ?>>Pending</option>
                <option value="Processing" <?= $data['status']=="Processing"?'selected':'' ?>>Processing</option>
                <option value="Completed" <?= $data['status']=="Completed"?'selected':'' ?>>Completed</option>
                <option value="Cancelled" <?= $data['status']=="Cancelled"?'selected':'' ?>>Cancelled</option>
            </select>

            <button class="btn btn-sm btn-primary" name="update_order_status">
                Save
            </button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>


<?php elseif($page=="custom"): ?>

<h2>Custom Orders</h2>

<table class="table table-bordered table-hover align-middle">
<thead class="table-light">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Product</th>
    <th>Description</th>
    <th>Image</th>
    <th>Status</th>
    <th>Update</th>
</tr>
</thead>

<tbody>
<?php
$res = $conn->query("SELECT * FROM custom_orders ORDER BY created_at DESC");
while($r = $res->fetch_assoc()):
?>
<tr>
    <td>#<?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= htmlspecialchars($r['product_type']) ?></td>
    <td><?= htmlspecialchars($r['description']) ?></td>

    <!-- IMAGE COLUMN -->
    <td>
        <?php if(!empty($r['reference_image'])): ?>
            <img src="<?= htmlspecialchars($r['reference_image']) ?>"
                 width="80" height="80"
                 style="object-fit:cover;border-radius:8px;">
        <?php else: ?>
            <span class="text-muted">No Image</span>
        <?php endif; ?>
    </td>

    <!-- STATUS -->
    <td>
        <span class="badge bg-warning"><?= htmlspecialchars($r['status']) ?></span>
    </td>

    <!-- UPDATE -->
    <td>
        <form method="post" class="d-flex gap-2">
            <input type="hidden" name="custom_id" value="<?= $r['id'] ?>">

            <select name="status" class="form-select form-select-sm">
                <option value="Pending"   <?= $r['status']=="Pending"?'selected':'' ?>>Pending</option>
                <option value="Reviewing" <?= $r['status']=="Reviewing"?'selected':'' ?>>Reviewing</option>
                <option value="Priced"    <?= $r['status']=="Priced"?'selected':'' ?>>Priced</option>
                <option value="Accepted"  <?= $r['status']=="Accepted"?'selected':'' ?>>Accepted</option>
                <option value="Rejected"  <?= $r['status']=="Rejected"?'selected':'' ?>>Rejected</option>
            </select>

            <button class="btn btn-sm btn-primary" name="update_custom_status">
                Save
            </button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>


<?php elseif($page=="users"): ?>

<h2>Users</h2>
<table class="table table-bordered">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Admin</th></tr>
<?php
$res=$conn->query("SELECT id,full_name,email,is_admin FROM users");
while($r=$res->fetch_assoc()):
?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['full_name']) ?></td>
<td><?= htmlspecialchars($r['email']) ?></td>
<td><?= $r['is_admin']==1?'Yes':'No' ?></td>
</tr>
<?php endwhile; ?>
</table>

    <?php elseif($page=="reviews"): ?>

<h2>Reviews</h2>

<table class="table table-bordered table-hover align-middle">
<thead class="table-light">
<tr>
    <th>User</th>
    <th>Product</th>
    <th>Rating</th>
    <th>Review</th>
    <th>Images</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php
$res = $conn->query("
    SELECT r.*, u.full_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
");

while($r = $res->fetch_assoc()):
    // Decode JSON photos
    $photos = json_decode($r['photos'], true);
?>
<tr>
    <td><?= htmlspecialchars($r['full_name']) ?></td>

    <td>Product #<?= htmlspecialchars($r['product_id']) ?></td>

    <td><?= str_repeat("⭐", (int)$r['rating']) ?></td>

    <td><?= htmlspecialchars($r['review_text']) ?></td>

    <!-- SHOW IMAGES -->
    <td>
        <?php if(!empty($photos)): ?>
            <?php foreach($photos as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" 
                     width="60" height="60" 
                     style="object-fit:cover;border-radius:6px;margin:2px;">
            <?php endforeach; ?>
        <?php else: ?>
            <span class="text-muted">No Image</span>
        <?php endif; ?>
    </td>

    <td>
        <form method="post" onsubmit="return confirm('Delete this review?')">
            <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
            <button class="btn btn-sm btn-danger" name="delete_review">Delete</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>


<?php elseif($page=="messages"): ?>

<h2>Messages</h2>
<table class="table table-bordered">
<tr><th>Name</th><th>Email</th><th>Message</th></tr>
<?php
$res=$conn->query("SELECT * FROM contact_messages");
while($r=$res->fetch_assoc()):
?>
<tr>
<td><?= htmlspecialchars($r['name']) ?></td>
<td><?= htmlspecialchars($r['email']) ?></td>
<td><?= htmlspecialchars($r['message']) ?></td>
</tr>
<?php endwhile; ?>
</table>

<?php elseif($page=="products"): ?>

<h2>Products</h2>
<div class="row">
<?php foreach($products as $p): ?>
<div class="col-md-3">
<div class="card p-2">
<img src="<?= $p['images'][0] ?>" class="img-fluid">
<h6><?= htmlspecialchars($p['name']) ?></h6>
₹<?= $p['price'] ?>
</div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>

</div>
</div>
</div>

</body>
</html>
