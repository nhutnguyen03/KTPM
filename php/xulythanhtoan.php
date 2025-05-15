<?php
require_once ("../BackEnd/ConnectionDB/DB_classes.php");

if (!isset($_POST['request']) && !isset($_GET['request'])) die();

switch ($_POST['request']) {
    case 'themdonhang':
        $dulieu = $_POST["dulieu"];

        $hoadonBUS = new HoaDonBUS();
        $chitiethdBUS = new ChiTietHoaDonBUS();

        // Thêm hóa đơn
        $hoadonBUS->add_new(array(
            "MaHD" => "",
            "MaND" => $dulieu["maNguoiDung"],
            "NgayLap" => $dulieu["ngayLap"],
            "NguoiNhan" => $dulieu["tenNguoiNhan"],
            "SDT" => $dulieu["sdtNguoiNhan"],
            "DiaChi" => $dulieu["diaChiNguoiNhan"],
            "PhuongThucTT" => $dulieu["phuongThucTT"],
            "TongTien" => $dulieu["tongTien"],
            "TrangThai" => 1
        ));

        // Lấy ID hóa đơn mới nhất
        $hoadonMaxID = $hoadonBUS->get_list("SELECT * FROM hoadon ORDER BY MaHD DESC LIMIT 0, 1");
        $mahd = $hoadonMaxID[0]["MaHD"];

        // Thêm chi tiết hóa đơn
        foreach ($dulieu["dssp"] as $sp) {
            $dataSp = (new SanPhamBUS())->select_by_id("*", $sp["masp"]);
            $donGia = $dataSp["DonGia"];

            // Sử dụng mảng liên kết với key đúng tên cột
            $chitiethdBUS->add_new(array(
                "ma_hoa_don" => $mahd,
                "ma_san_pham" => $sp["masp"],
                "so_luong" => $sp["soLuong"],
                "don_gia" => $donGia
            ));
        }

        die(json_encode(['success' => true]));
    break;
}
?>