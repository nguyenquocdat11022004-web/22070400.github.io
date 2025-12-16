<?php
session_start();

// --- Exercise 1: Định nghĩa quyền và gán vai trò ---

// Cấu trúc các quyền cho từng vai trò
$roles = [
    'admin' => ['view_user', 'create_user', 'edit_user', 'delete_user'],
    'user'  => ['view_user', 'edit_own_profile'],
    'guest' => ['view_user']
];

// Danh sách người dùng giả lập (Database simulation)
$users = [
    101 => ['name' => 'An', 'role' => 'admin'],
    102 => ['name' => 'Bình', 'role' => 'user'],
    103 => ['name' => 'Chi', 'role' => 'guest']
];

// --- Exercise 2: Hàm kiểm tra quyền dựa trên User ID ---

/**
 * Kiểm tra một User cụ thể có quyền thực hiện hành động nào đó không
 */
function hasPermission($user_id, $permission) {
    global $users, $roles;
    
    // Kiểm tra nếu User ID tồn tại trong hệ thống
    if (!isset($users[$user_id])) return false;
    
    $user_role = $users[$user_id]['role'];
    
    // Kiểm tra quyền trong mảng vai trò
    return in_array($permission, $roles[$user_role]);
}

// --- Exercise 3: Quản lý quyền dựa trên Session (Thực tế khi đăng nhập) ---

// Giả lập sau khi User ID 101 đăng nhập thành công
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 101;
    $_SESSION['user_role'] = $users[101]['role']; // 'admin'
}

/**
 * Kiểm tra quyền của người dùng hiện đang đăng nhập trong Session
 */
function checkAccess($required_permission) {
    global $roles;
    
    // Nếu chưa đăng nhập, mặc định là guest
    $user_role = $_SESSION['user_role'] ?? 'guest';
    
    return in_array($required_permission, $roles[$user_role]);
}

// --- HIỂN THỊ KẾT QUẢ GIAO DIỆN ---

echo "<h2>Hệ thống quản lý quyền (RBAC)</h2>";
echo "Người dùng đang đăng nhập: <b>" . $users[$_SESSION['user_id']]['name'] . "</b>";
echo " (Vai trò: " . $_SESSION['user_role'] . ")<br><br>";

// Ứng dụng thực tế: Hiển thị các nút chức năng dựa trên quyền
echo "<div style='border: 1px solid #ccc; padding: 10px;'>";
    echo "<h3>Bảng điều khiển</h3>";
    
    if (checkAccess('view_user')) {
        echo "<button>Xem danh sách</button> ";
    }

    if (checkAccess('edit_user')) {
        echo "<button style='background: orange;'>Chỉnh sửa User</button> ";
    }

    if (checkAccess('delete_user')) {
        echo "<button style='background: red; color: white;'>Xóa User</button>";
    }
echo "</div>";

// Kiểm tra thử cho một ID khác (Exercise 2)
echo "<br>Kiểm tra quyền rời rạc (User 102):<br>";
echo "Bình có quyền xóa user không? " . (hasPermission(102, 'delete_user') ? "Có" : "Không");
?>