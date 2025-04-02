<?php
    require_once('../BackEnd/ConnectionDB/DB_classes.php');

    if(!isset($_POST['request'])) die();

    switch ($_POST['request']) {
    	case 'them':
            $hoTen = $_POST['hoTen'];
            $soDienThoai = $_POST['soDienThoai'];
            $diaChi = $_POST['diaChi'];
            $ghiChu = $_POST['ghiChu'];
            
            // Lấy thông tin giỏ hàng từ localStorage
            $gioHang = json_decode($_POST['gioHang'], true);
            
            if(empty($gioHang)) {
                die(json_encode([
                    'success' => false,
                    'message' => 'Giỏ hàng trống'
                ]));
            }
            
            $hoadonBUS = new HoaDonBUS();
            $chitiethoadonBUS = new ChiTietHoaDonBUS();
            
            // Tạo mã đơn hàng mới
            $maHD = "HD" . date("YmdHis");
            
            // Thêm đơn hàng mới
            $hoadonBUS->insert(array(
                "MaHD" => $maHD,
                "MaND" => isset($_SESSION['user']) ? $_SESSION['user']['MaND'] : null,
                "NgayLap" => date("Y-m-d H:i:s"),
                "TrangThai" => 1,
                "GhiChu" => $ghiChu
            ));
            
            // Thêm chi tiết đơn hàng
            foreach($gioHang as $item) {
                $chitiethoadonBUS->insert(array(
                    "MaHD" => $maHD,
                    "MaSP" => $item['maSP'],
                    "SoLuong" => $item['soLuong'],
                    "DonGia" => $item['donGia']
                ));
            }
            
            die(json_encode([
                'success' => true,
                'message' => 'Đặt hàng thành công'
            ]));
            break;

    	case 'capnhattrangthai':
            $maHD = $_POST['maHD'];
            $trangThai = $_POST['trangThai'];
            
            $hoadonBUS = new HoaDonBUS();
            
            // Kiểm tra trạng thái hiện tại của đơn hàng
            $hoaDon = $hoadonBUS->select_by_id("*", $maHD);
            
            if(!$hoaDon) {
                die(json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ]));
            }
            
            // Chỉ cho phép cập nhật từ trạng thái mới hoặc đã gửi vận chuyển
            if($hoaDon['TrangThai'] != 1 && $hoaDon['TrangThai'] != 2) {
                die(json_encode([
                    'success' => false,
                    'message' => 'Không thể cập nhật trạng thái đơn hàng đã hoàn thành hoặc đã hủy'
                ]));
            }
            
            // Nếu đơn hàng đang ở trạng thái "Đã gửi vận chuyển" và được duyệt
            if($hoaDon['TrangThai'] == 2 && $trangThai == 2) {
                $trangThai = 3; // Chuyển sang trạng thái "Đã hoàn thành"
            }
            
            // Cập nhật trạng thái
            $hoadonBUS->update("hoadon", array(
                "TrangThai" => $trangThai
            ), "MaHD = '$maHD'");
            
            die(json_encode([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]));
            break;

    	case 'getall':
				$hoadonBUS = new HoaDonBUS();
				$list = $hoadonBUS->select_all();
		    	die (json_encode($list));
    		break;

		default:
	    		die(json_encode([
                    'success' => false,
                    'message' => 'Yêu cầu không hợp lệ'
                ]));
	    		break;
    }
?>