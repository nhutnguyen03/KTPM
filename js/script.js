// Xác nhận đơn hàng
function xacNhanDonHang() {
    var hoTen = document.getElementById("hoTen").value;
    var soDienThoai = document.getElementById("soDienThoai").value;
    var diaChi = document.getElementById("diaChi").value;
    var ghiChu = document.getElementById("ghiChu").value;

    if (!hoTen || !soDienThoai || !diaChi) {
        Swal.fire({
            type: "error",
            title: "Lỗi",
            text: "Vui lòng điền đầy đủ thông tin"
        });
        return;
    }

    $.ajax({
        type: "POST",
        url: "php/xulydonhang.php",
        dataType: "json",
        data: {
            request: "them",
            hoTen: hoTen,
            soDienThoai: soDienThoai,
            diaChi: diaChi,
            ghiChu: ghiChu
        },
        success: function(data) {
            if(data.success) {
                Swal.fire({
                    type: "success",
                    title: "Đặt hàng thành công",
                    text: "Cảm ơn bạn đã mua hàng tại cửa hàng chúng tôi"
                }).then(() => {
                    // Xóa giỏ hàng
                    localStorage.removeItem("gioHang");
                    // Đóng modal thanh toán
                    $('#modalThanhToan').modal('hide');
                    // Cập nhật số lượng sản phẩm trong giỏ hàng
                    capNhatSoLuongGioHang();
                    // Tải lại trang
                    location.reload();
                });
            } else {
                Swal.fire({
                    type: "error",
                    title: "Lỗi",
                    text: data.message
                });
            }
        },
        error: function(e) {
            Swal.fire({
                type: "error",
                title: "Lỗi đặt hàng",
                html: e.responseText
            });
        }
    });
} 