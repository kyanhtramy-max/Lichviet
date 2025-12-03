-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 23, 2025 lúc 04:51 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `lichviet`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('date','kethon','laman','xaynha','sinhcon') DEFAULT 'date',
  `solar_date` date DEFAULT NULL,
  `lunar_date` varchar(50) DEFAULT NULL,
  `rating_text` varchar(255) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `item_data` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `type`, `solar_date`, `lunar_date`, `rating_text`, `score`, `item_data`, `created_at`) VALUES
(32, 5, 'date', '2029-01-01', 'Sinh con: Cha 1997 - Mẹ 2004', 'Điểm: 8/10 - Dụng thần: Mộc', 8.00, NULL, '2025-11-20 20:44:04'),
(51, 5, 'date', '2004-09-05', '21-07-2004', 'Ngày xấu (bất lợi)', -0.50, NULL, '2025-11-21 14:18:28'),
(52, 5, 'date', '1997-01-13', '5-12-1996', 'Ngày sinh: 1997-01-13 - Giản Hạ Thủy - Ma Kết', 5.00, NULL, '2025-11-21 14:19:42'),
(54, 5, 'date', '2025-11-21', 'Năm 1980 - Nam', 'Xem hướng nhà - Cung Khôn. Hướng tốt: Tây Nam, Tây Bắc, Tây, Đông Bắc', 8.50, NULL, '2025-11-21 14:22:48'),
(77, 5, 'date', '2027-01-01', 'Xây nhà 1973 → 2027', 'Xây nhà: NÊN LÀM - Điểm: 4', 4.00, NULL, '2025-11-22 02:01:07'),
(82, 7, 'date', '1997-01-13', '05-12-1996', 'Ngày tốt (cát lợi)', 2.50, NULL, '2025-11-22 13:49:56'),
(83, 7, 'date', '2004-09-05', '21-7-2004', 'Ngày sinh: 2004-09-05 - Tuyền Trung Thủy - Xử Nữ', 5.00, NULL, '2025-11-22 13:50:40'),
(84, 7, 'date', '1997-11-24', '25-10-1997', 'Ngày sinh: 1997-11-24 - Giản Hạ Thủy - Nhân Mã', 5.00, NULL, '2025-11-22 14:00:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history_chuyenngay`
--

CREATE TABLE `history_chuyenngay` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `input_type` enum('duong','am') NOT NULL,
  `input_date` date NOT NULL,
  `converted_type` enum('duong','am') NOT NULL,
  `converted_value` varchar(100) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history_chuyenngay`
--

INSERT INTO `history_chuyenngay` (`id`, `user_id`, `input_type`, `input_date`, `converted_type`, `converted_value`, `note`, `created_at`) VALUES
(1, 1, 'duong', '2025-01-01', 'am', '01/12 Giáp Thìn', 'Chuyển ngày đầu năm dương sang âm', '2025-11-19 07:53:36'),
(2, NULL, 'am', '2025-01-10', 'duong', '2025-02-09', 'Chuyển ngày âm để xem sinh nhật dương.', '2025-11-19 07:53:36'),
(3, 6, 'duong', '2023-10-15', 'am', '01-09-2023', 'Ngày xấu (bất lợi)', '2025-11-19 16:12:30'),
(4, 6, 'am', '2023-09-01', 'duong', '2023-10-15', 'Ngày xấu (bất lợi)', '2025-11-19 16:12:34'),
(5, 6, 'duong', '2023-10-15', 'am', '01-09-2023', 'Ngày xấu (bất lợi)', '2025-11-19 16:12:40'),
(6, 6, 'duong', '2023-10-15', 'am', '01-09-2023', 'Ngày xấu (bất lợi)', '2025-11-19 16:15:24'),
(7, 6, 'am', '2023-09-01', 'duong', '2023-10-15', 'Ngày xấu (bất lợi)', '2025-11-19 16:15:27'),
(8, 5, 'am', '2025-08-01', 'duong', '2025-09-22', 'Ngày bình thường', '2025-11-19 17:06:38'),
(9, 5, 'am', '2004-05-23', 'duong', '2004-07-10', 'Ngày xấu (bất lợi)', '2025-11-19 17:06:56'),
(10, 5, 'duong', '2025-11-19', 'am', '30-09-2025', 'Ngày xấu (bất lợi)', '2025-11-19 17:54:09'),
(11, 5, 'duong', '2025-11-19', 'am', '30-09-2025', 'Ngày xấu (bất lợi)', '2025-11-19 18:02:33'),
(12, 5, 'am', '2025-08-01', 'duong', '2025-09-22', 'Ngày bình thường', '2025-11-19 18:04:19'),
(13, 5, 'duong', '2025-11-19', 'am', '30-09-2025', 'Ngày xấu (bất lợi)', '2025-11-19 18:34:20'),
(14, 5, 'am', '2025-09-30', 'duong', '2025-11-19', 'Ngày xấu (bất lợi)', '2025-11-19 18:34:23'),
(15, 5, 'am', '2025-08-01', 'duong', '2025-09-22', 'Ngày bình thường', '2025-11-19 18:40:27'),
(16, 5, 'duong', '2025-11-19', 'am', '30-09-2025', 'Ngày xấu (bất lợi)', '2025-11-19 18:40:48'),
(17, 5, 'duong', '2025-11-23', 'am', '04-10-2025', 'Ngày xấu (bất lợi)', '2025-11-19 18:41:11'),
(18, 5, 'am', '2025-08-01', 'duong', '2025-09-22', 'Ngày bình thường', '2025-11-19 18:41:26'),
(19, 5, 'duong', '2025-11-10', 'am', '21-09-2025', 'Ngày xấu (bất lợi)', '2025-11-19 18:52:28'),
(20, 5, 'am', '2025-08-09', 'duong', '2025-09-30', 'Ngày tốt (cát lợi)', '2025-11-19 19:01:29'),
(21, 5, 'duong', '2027-11-19', 'am', '22-10-2027', 'Ngày xấu (bất lợi)', '2025-11-19 19:01:49'),
(22, 5, 'duong', '2024-01-15', 'am', '15-12-2023', 'Ngày tốt - Test', '2025-11-19 19:19:31'),
(23, 5, 'duong', '2025-11-19', 'am', '30-09-2025', 'Ngày xấu (bất lợi)', '2025-11-19 19:19:38'),
(24, 5, 'duong', '2030-11-19', 'am', '24-10-2030', 'Ngày bình thường', '2025-11-19 19:19:54'),
(25, 5, 'duong', '2024-01-15', 'am', '15-12-2023', 'Ngày tốt - Test', '2025-11-19 19:21:29'),
(26, 5, 'duong', '2024-01-15', 'am', '15-12-2023', 'Ngày tốt - Test', '2025-11-19 19:21:30'),
(27, 5, 'duong', '2024-01-15', 'am', '15-12-2023', 'Ngày tốt - Test', '2025-11-19 19:21:30'),
(28, 5, 'duong', '2024-01-15', 'am', '15-12-2023', 'Ngày tốt - Test', '2025-11-19 19:21:30'),
(29, 5, 'duong', '2025-11-19', 'am', '30-09-2025', 'Ngày xấu (bất lợi)', '2025-11-19 19:41:05'),
(30, 5, 'duong', '2004-09-05', 'am', '21-07-2004', 'Ngày xấu (bất lợi)', '2025-11-19 19:41:21'),
(31, 5, 'am', '2007-08-01', 'duong', '2007-09-11', 'Ngày xấu (bất lợi)', '2025-11-19 19:41:57'),
(32, 5, 'duong', '2088-11-19', 'am', '07-10-2088', 'Ngày tốt (cát lợi)', '2025-11-19 19:42:22'),
(33, 5, 'am', '2025-06-01', 'duong', '2025-06-25', 'Ngày xấu (bất lợi)', '2025-11-19 19:50:12'),
(34, 5, 'am', '2025-08-01', 'duong', '2025-09-22', 'Ngày bình thường', '2025-11-20 06:35:16'),
(35, 5, 'duong', '2025-10-20', 'am', '29-08-2025', 'Ngày xấu (bất lợi)', '2025-11-20 07:50:28'),
(36, 5, 'am', '2025-09-29', 'duong', '2025-11-18', 'Ngày xấu (bất lợi)', '2025-11-20 07:50:35'),
(37, 5, 'duong', '2025-11-30', 'am', '11-10-2025', 'Ngày xấu (bất lợi)', '2025-11-20 10:29:36'),
(38, 5, 'am', '2025-10-11', 'duong', '2025-11-30', 'Ngày xấu (bất lợi)', '2025-11-20 10:29:46'),
(39, 5, 'duong', '2004-09-05', 'am', '21-07-2004', 'Ngày xấu (bất lợi)', '2025-11-20 12:46:19'),
(40, 5, 'duong', '2019-11-20', 'am', '24-10-2019', 'Ngày bình thường', '2025-11-20 15:35:28'),
(41, 5, 'am', '2019-10-24', 'duong', '2019-11-20', 'Ngày bình thường', '2025-11-20 15:35:31'),
(42, 5, 'am', '2019-10-24', 'duong', '2019-11-20', 'Ngày bình thường', '2025-11-20 15:35:33'),
(43, 5, 'am', '2019-10-24', 'duong', '2019-11-20', 'Ngày bình thường', '2025-11-20 15:35:37'),
(44, 5, 'duong', '2025-11-20', 'am', '01-10-2025', 'Ngày xấu (bất lợi)', '2025-11-20 19:27:47'),
(45, 5, 'duong', '2025-11-20', 'am', '01-10-2025', 'Ngày xấu (bất lợi)', '2025-11-20 19:27:48'),
(46, 5, 'duong', '2025-11-20', 'am', '01-10-2025', 'Ngày xấu (bất lợi)', '2025-11-20 21:56:56'),
(47, 5, 'duong', '2025-11-20', 'am', '01-10-2025', 'Ngày xấu (bất lợi)', '2025-11-20 21:57:46'),
(48, 5, 'am', '2025-10-01', 'duong', '2025-11-20', 'Ngày xấu (bất lợi)', '2025-11-20 21:57:47'),
(49, 5, 'duong', '2025-11-21', 'am', '02-10-2025', 'Ngày bình thường', '2025-11-21 07:10:13'),
(50, 5, 'am', '2025-10-02', 'duong', '2025-11-21', 'Ngày bình thường', '2025-11-21 07:10:14'),
(51, 5, 'duong', '2025-11-21', 'am', '02-10-2025', 'Ngày bình thường', '2025-11-21 07:17:15'),
(52, 5, 'am', '2025-10-02', 'duong', '2025-11-21', 'Ngày bình thường', '2025-11-21 07:17:21'),
(53, 5, 'am', '2025-09-02', 'duong', '2025-10-22', 'Ngày xấu (bất lợi)', '2025-11-21 07:17:27'),
(54, 5, 'duong', '2004-05-09', 'am', '21-03-2004', 'Ngày xấu (bất lợi)', '2025-11-21 07:18:11'),
(55, 5, 'duong', '2004-09-05', 'am', '21-07-2004', 'Ngày xấu (bất lợi)', '2025-11-21 07:18:22'),
(56, 5, 'am', '1996-12-15', 'duong', '1997-01-23', 'Ngày xấu (bất lợi)', '2025-11-21 07:18:42'),
(57, 5, 'duong', '2003-11-21', 'am', '28-10-2003', 'Ngày bình thường', '2025-11-21 09:13:15'),
(58, 5, 'duong', '2028-01-01', 'am', '05-12-2027', 'Ngày xấu (bất lợi)', '2025-11-21 12:56:58'),
(59, 5, 'am', '2025-08-01', 'duong', '2025-09-22', 'Ngày bình thường', '2025-11-21 14:59:39'),
(60, 5, 'duong', '2025-11-21', 'am', '02-10-2025', 'Ngày bình thường', '2025-11-21 15:00:00'),
(61, 5, 'duong', '2025-11-21', 'am', '02-10-2025', 'Ngày bình thường', '2025-11-21 17:18:29'),
(62, 5, 'am', '2025-09-02', 'duong', '2025-10-22', 'Ngày xấu (bất lợi)', '2025-11-21 17:18:43'),
(63, 5, 'duong', '2025-11-21', 'am', '02-10-2025', 'Ngày bình thường', '2025-11-21 17:19:02'),
(64, 5, 'am', '2025-09-02', 'duong', '2025-10-22', 'Ngày xấu (bất lợi)', '2025-11-21 17:19:05'),
(65, 5, 'am', '2025-09-02', 'duong', '2025-10-22', 'Ngày xấu (bất lợi)', '2025-11-21 17:21:04'),
(66, 5, 'am', '2025-08-01', 'duong', '2025-09-22', 'Ngày bình thường', '2025-11-21 19:05:17'),
(67, 5, 'duong', '2025-11-21', 'am', '02-10-2025', 'Ngày bình thường', '2025-11-21 19:05:34'),
(68, 5, 'am', '2025-10-02', 'duong', '2025-11-21', 'Ngày bình thường', '2025-11-21 19:05:39'),
(69, 5, 'am', '2025-07-01', 'duong', '2025-08-23', 'Ngày bình thường', '2025-11-21 19:28:03'),
(70, 5, 'duong', '2034-11-21', 'am', '11-10-2034', 'Ngày xấu (bất lợi)', '2025-11-21 20:13:32'),
(71, 7, 'duong', '2025-11-22', 'am', '03-10-2025', 'Ngày tốt (cát lợi)', '2025-11-22 06:48:10'),
(72, 7, 'am', '2025-09-03', 'duong', '2025-10-23', 'Ngày xấu (bất lợi)', '2025-11-22 06:48:18'),
(73, 7, 'duong', '1997-01-13', 'am', '05-12-1996', 'Ngày tốt (cát lợi)', '2025-11-22 06:49:51'),
(74, 6, 'duong', '2025-11-22', 'am', '03-10-2025', 'Ngày tốt (cát lợi)', '2025-11-22 19:10:31'),
(75, 6, 'am', '2026-08-15', 'duong', '2026-09-25', 'Ngày tốt (cát lợi)', '2025-11-23 08:38:34'),
(76, 6, 'am', '2026-08-15', 'duong', '2026-09-25', 'Ngày tốt (cát lợi)', '2025-11-23 08:38:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history_huongnha`
--

CREATE TABLE `history_huongnha` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `owner_year` int(11) NOT NULL,
  `current_direction` varchar(50) DEFAULT NULL,
  `good_directions` text DEFAULT NULL,
  `bad_directions` text DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history_huongnha`
--

INSERT INTO `history_huongnha` (`id`, `user_id`, `owner_year`, `current_direction`, `good_directions`, `bad_directions`, `summary`, `created_at`) VALUES
(1, 1, 1990, 'Đông Nam', 'Hướng Đông, Đông Nam, Nam là các hướng cát lợi, tài vận hanh thông.', 'Hướng Tây Bắc, Tây là các hướng nên hạn chế mở cửa chính.', 'Hướng đang chọn khá tốt, hợp mệnh, mang lại sinh khí.', '2025-11-19 07:53:37'),
(2, NULL, 1985, 'Tây', 'Hướng Tây Nam, Tây Bắc tốt hơn cho sức khỏe và gia đạo.', 'Hướng Đông, Đông Nam có thể gây bất lợi về công việc.', 'Nên cân nhắc thay đổi bố trí nội thất hoặc hướng bàn thờ để hóa giải.', '2025-11-19 07:53:37'),
(3, 5, 1980, 'Tây Nam', 'Tây Nam, Tây Bắc, Tây, Đông Bắc', 'Bắc, Đông, Đông Nam, Nam', 'Xem hướng nhà - Năm sinh: 1980, Giới tính: Nam, Cung phi: Khôn. Hướng tốt: Tây Nam, Tây Bắc, Tây, Đông Bắc. Hướng xấu: Bắc, Đông, Đông Nam, Nam', '2025-11-20 14:39:30'),
(4, 5, 1980, 'Tây Nam', 'Tây Nam, Tây Bắc, Tây, Đông Bắc', 'Bắc, Đông, Đông Nam, Nam', 'Xem hướng nhà - Năm sinh: 1980, Giới tính: Nam, Cung phi: Khôn. Hướng tốt: Tây Nam, Tây Bắc, Tây, Đông Bắc. Hướng xấu: Bắc, Đông, Đông Nam, Nam', '2025-11-20 19:28:14'),
(5, 5, 1980, 'Tây Nam', 'Tây Nam, Tây Bắc, Tây, Đông Bắc', 'Bắc, Đông, Đông Nam, Nam', 'Xem hướng nhà - Năm sinh: 1980, Giới tính: Nam, Cung phi: Khôn. Hướng tốt: Tây Nam, Tây Bắc, Tây, Đông Bắc. Hướng xấu: Bắc, Đông, Đông Nam, Nam', '2025-11-21 07:22:44'),
(6, 7, 1980, 'Tây Nam', 'Tây Nam, Tây Bắc, Tây, Đông Bắc', 'Bắc, Đông, Đông Nam, Nam', 'Xem hướng nhà - Năm sinh: 1980, Giới tính: Nam, Cung phi: Khôn. Hướng tốt: Tây Nam, Tây Bắc, Tây, Đông Bắc. Hướng xấu: Bắc, Đông, Đông Nam, Nam', '2025-11-22 06:53:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history_kethon`
--

CREATE TABLE `history_kethon` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `male_year` int(11) NOT NULL,
  `female_year` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `evaluation` varchar(255) DEFAULT NULL,
  `remedies` text DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history_kethon`
--

INSERT INTO `history_kethon` (`id`, `user_id`, `male_year`, `female_year`, `score`, `evaluation`, `remedies`, `detail`, `created_at`) VALUES
(1, 1, 1995, 1997, 88, 'Tuổi kết hôn đẹp', 'Có thể chọn thêm ngày giờ cưới hợp mệnh để tăng cát khí.', 'Thiên can địa chi hài hòa, gia đình êm ấm, con cái thuận hòa.', '2025-11-19 07:53:36'),
(2, NULL, 1989, 1993, 55, 'Tuổi kết hôn trung bình', 'Có thể hóa giải bằng việc chọn năm cưới, màu sắc trang trí, hướng nhà phù hợp.', 'Có một số xung khắc nhỏ về can chi, cần chú ý trong giao tiếp vợ chồng.', '2025-11-19 07:53:36'),
(18, 5, 1996, 1998, 10, 'RẤT TỐT', 'Hôn nhân hạnh phúc, tiếp tục duy trì sự thấu hiểu', '{\\\"husbandBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Bính\\\",\\\"chi\\\":\\\"Tý\\\"},\\\"month\\\":{\\\"can\\\":\\\"Canh\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"day\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Đinh\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"elements\\\":{\\\"Mộc\\\":3,\\\"Hỏa\\\":3,\\\"Thổ\\\":0,\\\"Kim\\\":1,\\\"Thủy\\\":1},\\\"strength\\\":\\\"Thân vượng\\\",\\\"usefulGod\\\":\\\"Kim\\\",\\\"avoidGod\\\":\\\"Thủy\\\",\\\"lunar\\\":{\\\"day\\\":29,\\\"month\\\":1,\\\"year\\\":1996,\\\"leap\\\":0}},\\\"wifeBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Mậu\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"month\\\":{\\\"can\\\":\\\"Mậu\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"day\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Đinh\\\",\\\"chi\\\":\\\"Mùi\\\"},\\\"elements\\\":{\\\"Mộc\\\":2,\\\"Hỏa\\\":3,\\\"Thổ\\\":3,\\\"Kim\\\":0,\\\"Thủy\\\":0},\\\"strength\\\":\\\"Thân nhược\\\",\\\"usefulGod\\\":\\\"Thủy\\\",\\\"avoidGod\\\":\\\"Kim\\\",\\\"lunar\\\":{\\\"day\\\":24,\\\"month\\\":5,\\\"year\\\":1998,\\\"leap\\\":0}},\\\"analysis\\\":{\\\"score\\\":10,\\\"details\\\":[{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Dụng thần tương sinh (Kim ↔ Thủy) - Tốt\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Kỵ thần được hóa giải - Tốt\\\"},{\\\"type\\\":\\\"neutral\\\",\\\"text\\\":\\\"ℹ️ Thiên Can bình thường\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ 2 tam hợp 1 lục hợp \\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Cung Phi tương hợp - Tốt cho hậu vận\\\"}]}}', '2025-11-21 21:04:39'),
(20, 5, 1996, 1998, 10, 'RẤT TỐT', 'Hôn nhân hạnh phúc, tiếp tục duy trì sự thấu hiểu', '{\\\"husbandBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Bính\\\",\\\"chi\\\":\\\"Tý\\\"},\\\"month\\\":{\\\"can\\\":\\\"Canh\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"day\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Đinh\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"elements\\\":{\\\"Mộc\\\":3,\\\"Hỏa\\\":3,\\\"Thổ\\\":0,\\\"Kim\\\":1,\\\"Thủy\\\":1},\\\"strength\\\":\\\"Thân vượng\\\",\\\"usefulGod\\\":\\\"Kim\\\",\\\"avoidGod\\\":\\\"Thủy\\\",\\\"lunar\\\":{\\\"day\\\":29,\\\"month\\\":1,\\\"year\\\":1996,\\\"leap\\\":0}},\\\"wifeBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Mậu\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"month\\\":{\\\"can\\\":\\\"Mậu\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"day\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Đinh\\\",\\\"chi\\\":\\\"Mùi\\\"},\\\"elements\\\":{\\\"Mộc\\\":2,\\\"Hỏa\\\":3,\\\"Thổ\\\":3,\\\"Kim\\\":0,\\\"Thủy\\\":0},\\\"strength\\\":\\\"Thân nhược\\\",\\\"usefulGod\\\":\\\"Thủy\\\",\\\"avoidGod\\\":\\\"Kim\\\",\\\"lunar\\\":{\\\"day\\\":24,\\\"month\\\":5,\\\"year\\\":1998,\\\"leap\\\":0}},\\\"analysis\\\":{\\\"score\\\":10,\\\"details\\\":[{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Dụng thần tương sinh (Kim ↔ Thủy) - Tốt\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Kỵ thần được hóa giải - Tốt\\\"},{\\\"type\\\":\\\"neutral\\\",\\\"text\\\":\\\"ℹ️ Thiên Can bình thường\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ 2 tam hợp 1 lục hợp \\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Cung Phi tương hợp - Tốt cho hậu vận\\\"}]}}', '2025-11-21 21:04:39'),
(21, 5, 1996, 2004, 4, 'TRUNG BÌNH', 'Chọn năm kết hôn phù hợp với Dụng thần cả hai, Sử dụng vật phẩm phong thủy bổ trợ, Chọn hướng nhà theo Cung Phi', '{\\\"husbandBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Ất\\\",\\\"chi\\\":\\\"Hợi\\\"},\\\"month\\\":{\\\"can\\\":\\\"Mậu\\\",\\\"chi\\\":\\\"Tý\\\"},\\\"day\\\":{\\\"can\\\":\\\"Kỷ\\\",\\\"chi\\\":\\\"Dậu\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Kỷ\\\",\\\"chi\\\":\\\"Tuất\\\"},\\\"elements\\\":{\\\"Mộc\\\":1,\\\"Hỏa\\\":0,\\\"Thổ\\\":4,\\\"Kim\\\":1,\\\"Thủy\\\":2},\\\"strength\\\":\\\"Thân vượng\\\",\\\"usefulGod\\\":\\\"Mộc\\\",\\\"avoidGod\\\":\\\"Hỏa\\\",\\\"lunar\\\":{\\\"day\\\":23,\\\"month\\\":11,\\\"year\\\":1995,\\\"leap\\\":0}},\\\"wifeBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Thân\\\"},\\\"month\\\":{\\\"can\\\":\\\"Nhâm\\\",\\\"chi\\\":\\\"Thân\\\"},\\\"day\\\":{\\\"can\\\":\\\"Đinh\\\",\\\"chi\\\":\\\"Hợi\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Dậu\\\"},\\\"elements\\\":{\\\"Mộc\\\":2,\\\"Hỏa\\\":1,\\\"Thổ\\\":0,\\\"Kim\\\":3,\\\"Thủy\\\":2},\\\"strength\\\":\\\"Thân nhược\\\",\\\"usefulGod\\\":\\\"Mộc\\\",\\\"avoidGod\\\":\\\"Thủy\\\",\\\"lunar\\\":{\\\"day\\\":21,\\\"month\\\":7,\\\"year\\\":2004,\\\"leap\\\":0}},\\\"analysis\\\":{\\\"score\\\":4,\\\"details\\\":[{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Dụng thần tương đồng (Mộc) - Rất tốt\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Nhật chủ tương sinh (Thổ ↔ Hỏa) - Rất tốt\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ 1 cặp Thiên Can hợp hóa - Tốt\\\"},{\\\"type\\\":\\\"neutral\\\",\\\"text\\\":\\\"ℹ️ 1 tam hợp 1 tứ hành xung \\\"},{\\\"type\\\":\\\"warning\\\",\\\"text\\\":\\\"⚠️ Cung Phi cần xem xét - Nên chọn hướng nhà phù hợp\\\"}]}}', '2025-11-21 19:43:28'),
(22, 7, 1997, 2004, 7, 'TỐT', 'Hôn nhân hạnh phúc, tiếp tục duy trì sự thấu hiểu', '{\\\"husbandBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Bính\\\",\\\"chi\\\":\\\"Tý\\\"},\\\"month\\\":{\\\"can\\\":\\\"Tân\\\",\\\"chi\\\":\\\"Sửu\\\"},\\\"day\\\":{\\\"can\\\":\\\"Ất\\\",\\\"chi\\\":\\\"Mão\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Kỷ\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"elements\\\":{\\\"Mộc\\\":2,\\\"Hỏa\\\":2,\\\"Thổ\\\":2,\\\"Kim\\\":1,\\\"Thủy\\\":1},\\\"strength\\\":\\\"Thân vượng\\\",\\\"usefulGod\\\":\\\"Kim\\\",\\\"avoidGod\\\":\\\"Thủy\\\",\\\"lunar\\\":{\\\"day\\\":5,\\\"month\\\":12,\\\"year\\\":1996,\\\"leap\\\":0}},\\\"wifeBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Thân\\\"},\\\"month\\\":{\\\"can\\\":\\\"Nhâm\\\",\\\"chi\\\":\\\"Thân\\\"},\\\"day\\\":{\\\"can\\\":\\\"Đinh\\\",\\\"chi\\\":\\\"Hợi\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Quý\\\",\\\"chi\\\":\\\"Mùi\\\"},\\\"elements\\\":{\\\"Mộc\\\":1,\\\"Hỏa\\\":1,\\\"Thổ\\\":1,\\\"Kim\\\":2,\\\"Thủy\\\":3},\\\"strength\\\":\\\"Thân nhược\\\",\\\"usefulGod\\\":\\\"Mộc\\\",\\\"avoidGod\\\":\\\"Thủy\\\",\\\"lunar\\\":{\\\"day\\\":21,\\\"month\\\":7,\\\"year\\\":2004,\\\"leap\\\":0}},\\\"analysis\\\":{\\\"score\\\":7,\\\"details\\\":[{\\\"type\\\":\\\"bad\\\",\\\"text\\\":\\\"❌ Dụng thần tương khắc (Kim → Mộc) - Có xung đột\\\"},{\\\"type\\\":\\\"warning\\\",\\\"text\\\":\\\"⚠️ Âm Dương đồng loại - Trung bình\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Nhật chủ tương sinh (Mộc ↔ Hỏa) - Hôn nhân hòa hợp\\\"},{\\\"type\\\":\\\"neutral\\\",\\\"text\\\":\\\"ℹ️ Thiên Can trung lập\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ 2 tam hợp 1 lục hợp \\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Cung Phi hợp (Tốn - Ly) - Tốt cho hậu vận (+2)\\\"}]}}', '2025-11-22 07:39:43'),
(23, 7, 1996, 1998, 7, 'TỐT', 'Hôn nhân hạnh phúc, tiếp tục duy trì sự thấu hiểu', '{\\\"husbandBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Bính\\\",\\\"chi\\\":\\\"Tý\\\"},\\\"month\\\":{\\\"can\\\":\\\"Canh\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"day\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Đinh\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"elements\\\":{\\\"Mộc\\\":3,\\\"Hỏa\\\":3,\\\"Thổ\\\":0,\\\"Kim\\\":1,\\\"Thủy\\\":1},\\\"strength\\\":\\\"Thân vượng\\\",\\\"usefulGod\\\":\\\"Kim\\\",\\\"avoidGod\\\":\\\"Thủy\\\",\\\"lunar\\\":{\\\"day\\\":29,\\\"month\\\":1,\\\"year\\\":1996,\\\"leap\\\":0}},\\\"wifeBazi\\\":{\\\"year\\\":{\\\"can\\\":\\\"Mậu\\\",\\\"chi\\\":\\\"Dần\\\"},\\\"month\\\":{\\\"can\\\":\\\"Mậu\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"day\\\":{\\\"can\\\":\\\"Giáp\\\",\\\"chi\\\":\\\"Ngọ\\\"},\\\"hour\\\":{\\\"can\\\":\\\"Đinh\\\",\\\"chi\\\":\\\"Mùi\\\"},\\\"elements\\\":{\\\"Mộc\\\":2,\\\"Hỏa\\\":3,\\\"Thổ\\\":3,\\\"Kim\\\":0,\\\"Thủy\\\":0},\\\"strength\\\":\\\"Thân nhược\\\",\\\"usefulGod\\\":\\\"Thủy\\\",\\\"avoidGod\\\":\\\"Kim\\\",\\\"lunar\\\":{\\\"day\\\":24,\\\"month\\\":5,\\\"year\\\":1998,\\\"leap\\\":0}},\\\"analysis\\\":{\\\"score\\\":7.5,\\\"details\\\":[{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Dụng thần tương sinh (Kim ↔ Thủy) - Bổ trợ tốt\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ Kỵ thần được hóa giải bởi Dụng thần - Giảm thiểu bất lợi\\\"},{\\\"type\\\":\\\"warning\\\",\\\"text\\\":\\\"⚠️ Âm Dương đồng loại - Trung bình\\\"},{\\\"type\\\":\\\"neutral\\\",\\\"text\\\":\\\"ℹ️ Nhật chủ bình hòa - Không xung không khắc\\\"},{\\\"type\\\":\\\"neutral\\\",\\\"text\\\":\\\"ℹ️ Thiên Can trung lập\\\"},{\\\"type\\\":\\\"good\\\",\\\"text\\\":\\\"✅ 2 tam hợp 1 lục hợp \\\"},{\\\"type\\\":\\\"warning\\\",\\\"text\\\":\\\"⚠️ Cung Phi trung bình (Tốn - Tốn)\\\"}]}}', '2025-11-22 08:57:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history_laman`
--

CREATE TABLE `history_laman` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `self_year` int(11) NOT NULL,
  `partner_year` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `evaluation` varchar(255) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history_laman`
--

INSERT INTO `history_laman` (`id`, `user_id`, `self_year`, `partner_year`, `score`, `evaluation`, `detail`, `created_at`) VALUES
(1, 1, 1995, 1993, 90, 'Rất hợp tác trong làm ăn', 'Mệnh và thiên can phù hợp, dễ hỗ trợ nhau, thuận lợi mở rộng kinh doanh.', '2025-11-19 07:53:36'),
(2, NULL, 1990, 1985, 40, 'Không nên hợp tác lâu dài', 'Có nhiều yếu tố xung khắc, nếu hợp tác cần thỏa thuận rõ ràng, phân chia trách nhiệm.', '2025-11-19 07:53:36'),
(5, 5, 1980, 1985, -2, 'KHÔNG HỢP', '{\"personAYear\":1980,\"personBYear\":1985,\"businessYear\":2020,\"canA\":\"Canh\",\"chiA\":\"Thân\",\"menhA\":\"Mộc\",\"canB\":\"Ất\",\"chiB\":\"Sửu\",\"menhB\":\"Kim\",\"canBiz\":\"Canh\",\"chiBiz\":\"Tý\",\"menhBiz\":\"Thổ\",\"score\":-2,\"danhGia\":\"KHÔNG HỢP\",\"details\":[\"❌ Mệnh tương khắc: Dễ mâu thuẫn\"]}', '2025-11-21 09:01:52'),
(6, 5, 2004, 1997, 1, 'TỐT', '{\"personAYear\":2004,\"personBYear\":1997,\"businessYear\":2024,\"canA\":\"Giáp\",\"chiA\":\"Thân\",\"menhA\":\"Thủy\",\"canB\":\"Đinh\",\"chiB\":\"Sửu\",\"menhB\":\"Thủy\",\"canBiz\":\"Giáp\",\"chiBiz\":\"Thìn\",\"menhBiz\":\"Hỏa\",\"score\":1,\"danhGia\":\"TỐT\",\"details\":[\"⚠️ Mệnh bình hòa: Hợp tác ổn\"]}', '2025-11-20 20:24:58'),
(7, 5, 2004, 1996, 3, 'RẤT TỐT', '{\"personAYear\":2004,\"personBYear\":1996,\"businessYear\":2024,\"canA\":\"Giáp\",\"chiA\":\"Thân\",\"menhA\":\"Thủy\",\"canB\":\"Bính\",\"chiB\":\"Tý\",\"menhB\":\"Thủy\",\"canBiz\":\"Giáp\",\"chiBiz\":\"Thìn\",\"menhBiz\":\"Hỏa\",\"score\":3,\"danhGia\":\"RẤT TỐT\",\"details\":[\"⚠️ Mệnh bình hòa: Hợp tác ổn\",\"✅ Địa chi tam hợp: Đồng quan điểm\"]}', '2025-11-20 20:25:03'),
(8, 7, 1980, 1985, -2, 'KHÔNG HỢP', '{\"personAYear\":1980,\"personBYear\":1985,\"businessYear\":2024,\"canA\":\"Canh\",\"chiA\":\"Thân\",\"menhA\":\"Mộc\",\"canB\":\"Ất\",\"chiB\":\"Sửu\",\"menhB\":\"Kim\",\"canBiz\":\"Giáp\",\"chiBiz\":\"Thìn\",\"menhBiz\":\"Hỏa\",\"score\":-2,\"danhGia\":\"KHÔNG HỢP\",\"details\":[\"❌ Mệnh tương khắc: Dễ mâu thuẫn\"]}', '2025-11-22 06:52:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history_ngaysinh`
--

CREATE TABLE `history_ngaysinh` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `lunar_date` varchar(50) DEFAULT NULL,
  `zodiac` varchar(50) DEFAULT NULL,
  `destiny` varchar(100) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history_ngaysinh`
--

INSERT INTO `history_ngaysinh` (`id`, `user_id`, `birth_date`, `lunar_date`, `zodiac`, `destiny`, `summary`, `created_at`) VALUES
(1, 5, '2004-09-05', '21-07-2004', 'Xử Nữ', 'Giản Hạ Thủy', 'Ngày sinh: 2004-09-05 - Mệnh: Giản Hạ Thủy', '2025-11-21 08:21:40'),
(2, 5, '1997-01-13', '5-12-1996', 'Ma Kết', 'Giản Hạ Thủy', 'Ngày sinh: 1997-01-13 - Mệnh: Giản Hạ Thủy', '2025-11-21 08:21:40'),
(3, 5, '2025-11-21', '2-10-2025', 'Bọ Cạp', 'Phúc Đăng Hỏa', 'Ngày sinh: 21/11/2025 - Mệnh: Phúc Đăng Hỏa', '2025-11-21 08:21:48'),
(4, 5, '1999-11-21', '14-10-1999', 'Bọ Cạp', 'Thành Đầu Thổ', 'Ngày sinh: 21/11/1999 - Mệnh: Thành Đầu Thổ', '2025-11-21 08:22:53'),
(5, 5, '2025-11-21', '2-10-2025', 'Bọ Cạp', 'Phúc Đăng Hỏa', 'Ngày sinh: 21/11/2025 - Mệnh: Phúc Đăng Hỏa', '2025-11-21 09:12:12'),
(6, 5, '2003-11-21', '28-10-2003', 'Bọ Cạp', 'Dương Liễu Mộc', 'Ngày sinh: 21/11/2003 - Mệnh: Dương Liễu Mộc', '2025-11-21 09:12:54'),
(7, 5, '2003-11-21', '28-10-2003', 'Bọ Cạp', 'Dương Liễu Mộc', 'Ngày sinh: 21/11/2003 - Mệnh: Dương Liễu Mộc', '2025-11-21 13:34:38'),
(8, 5, '1998-11-21', '3-9-1998', 'Bọ Cạp', 'Thành Đầu Thổ', 'Ngày sinh: 21/11/1998 - Mệnh: Thành Đầu Thổ', '2025-11-21 13:37:28'),
(9, 5, '1998-08-15', '24-5-1998', 'Sư Tử', 'Thành Đầu Thổ', 'Ngày sinh: 15/8/1998 - Mệnh: Thành Đầu Thổ', '2025-11-21 13:37:58'),
(10, 7, '2004-09-05', '21-7-2004', 'Xử Nữ', 'Tuyền Trung Thủy', 'Ngày sinh: 5/9/2004 - Mệnh: Tuyền Trung Thủy', '2025-11-22 06:50:24'),
(11, 7, '2025-11-24', '5-10-2025', 'Nhân Mã', 'Phúc Đăng Hỏa', 'Ngày sinh: 24/11/2025 - Mệnh: Phúc Đăng Hỏa', '2025-11-22 06:51:05'),
(12, 7, '2025-11-22', '3-10-2025', 'Bọ Cạp', 'Phúc Đăng Hỏa', 'Ngày sinh: 22/11/2025 - Mệnh: Phúc Đăng Hỏa', '2025-11-22 07:00:03'),
(13, 7, '1997-11-24', '25-10-1997', 'Nhân Mã', 'Giản Hạ Thủy', 'Ngày sinh: 24/11/1997 - Mệnh: Giản Hạ Thủy', '2025-11-22 07:00:16'),
(14, 7, '2026-11-22', '14-10-2026', 'Bọ Cạp', 'Thiên Hà Thủy', 'Ngày sinh: 22/11/2026 - Mệnh: Thiên Hà Thủy', '2025-11-22 07:08:54'),
(15, 7, '2004-09-05', '21-7-2004', 'Xử Nữ', 'Tuyền Trung Thủy', 'Ngày sinh: 5/9/2004 - Mệnh: Tuyền Trung Thủy', '2025-11-22 07:09:18'),
(16, 7, '2025-11-22', '3-10-2025', 'Bọ Cạp', 'Phúc Đăng Hỏa', 'Ngày sinh: 22/11/2025 - Mệnh: Phúc Đăng Hỏa', '2025-11-22 08:58:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history_sinhcon`
--

CREATE TABLE `history_sinhcon` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `father_year` int(11) NOT NULL,
  `mother_year` int(11) NOT NULL,
  `child_year` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `evaluation` varchar(255) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history_sinhcon`
--

INSERT INTO `history_sinhcon` (`id`, `user_id`, `father_year`, `mother_year`, `child_year`, `score`, `evaluation`, `detail`, `created_at`) VALUES
(1, 1, 1995, 1999, 2028, 85, 'Tuổi sinh con khá tốt', 'Ngũ hành tương sinh, mệnh con hài hòa với bố mẹ, thuận lợi về gia đạo.', '2025-11-19 07:53:36'),
(2, NULL, 1990, 1992, 2026, 60, 'Mức độ trung bình', 'Có một số xung nhẹ, có thể hóa giải bằng việc chọn tháng sinh và đặt tên hợp mệnh.', '2025-11-19 07:53:36'),
(4, 5, 1985, 1990, 2028, 4, 'Trung bình', '{\"familyUsefulGod\":\"Mộc\",\"bestYear\":{\"year\":2028,\"bazi\":{\"year\":{\"can\":\"Nhâm\",\"chi\":\"Tý\"},\"elements\":{\"Mộc\":1},\"yearChi\":\"Tý\",\"yearCan\":\"Nhâm\",\"menh\":\"Mộc\"},\"score\":4,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-20 19:27:35'),
(5, 5, 1991, 1990, 2026, 5, 'Tốt', '{\"familyUsefulGod\":\"Thủy\",\"bestYear\":{\"year\":2026,\"bazi\":{\"year\":{\"can\":\"Canh\",\"chi\":\"Tuất\"},\"elements\":{\"Kim\":1},\"yearChi\":\"Tuất\",\"yearCan\":\"Canh\",\"menh\":\"Kim\"},\"score\":5,\"details\":[\"✅ Con sinh Dụng thần gia đình\",\"✅ Tam hợp với mẹ (Tuất - Tuất)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-20 12:44:05'),
(6, 5, 1991, 1990, 2028, 4, 'Trung bình', '{\"familyUsefulGod\":\"Mộc\",\"bestYear\":{\"year\":2028,\"bazi\":{\"year\":{\"can\":\"Nhâm\",\"chi\":\"Tý\"},\"elements\":{\"Mộc\":1},\"yearChi\":\"Tý\",\"yearCan\":\"Nhâm\",\"menh\":\"Mộc\"},\"score\":4,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-20 12:44:15'),
(7, 5, 1997, 2004, 2029, 8, 'Rất tốt', '{\"familyUsefulGod\":\"Mộc\",\"bestYear\":{\"year\":2029,\"bazi\":{\"year\":{\"can\":\"Quý\",\"chi\":\"Sửu\"},\"elements\":{\"Mộc\":1},\"yearChi\":\"Sửu\",\"yearCan\":\"Quý\",\"menh\":\"Mộc\"},\"score\":8,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\",\"✅ Tam hợp với cha (Tỵ - Sửu)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-20 15:40:01'),
(8, 5, 1997, 2004, 2024, 4, 'Trung bình', '{\"familyUsefulGod\":\"Thổ\",\"bestYear\":{\"year\":2024,\"bazi\":{\"year\":{\"can\":\"Mậu\",\"chi\":\"Thân\"},\"elements\":{\"Thổ\":1},\"yearChi\":\"Thân\",\"yearCan\":\"Mậu\",\"menh\":\"Thổ\"},\"score\":4,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\",\"✅ Tam hợp với mẹ (Tý - Thân)\",\"❌ Tứ xung với cha (Tỵ - Thân)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-20 15:40:00'),
(9, 5, 1985, 1997, 2029, 5, 'Tốt', '{\"familyUsefulGod\":\"Mộc\",\"bestYear\":{\"year\":2029,\"bazi\":{\"year\":{\"can\":\"Quý\",\"chi\":\"Sửu\"},\"elements\":{\"Mộc\":1},\"yearChi\":\"Sửu\",\"yearCan\":\"Quý\",\"menh\":\"Mộc\"},\"score\":5,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\",\"✅ Tam hợp với cha (Tỵ - Sửu)\",\"✅ Tam hợp với mẹ (Tỵ - Sửu)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-21 09:14:03'),
(10, 5, 1985, 1997, 2025, 5, 'Tốt', '{\"familyUsefulGod\":\"Kim\",\"bestYear\":{\"year\":2025,\"bazi\":{\"year\":{\"can\":\"Kỷ\",\"chi\":\"Dậu\"},\"elements\":{\"Thổ\":1},\"yearChi\":\"Dậu\",\"yearCan\":\"Kỷ\",\"menh\":\"Thổ\"},\"score\":5,\"details\":[\"✅ Con sinh Dụng thần gia đình\",\"✅ Tam hợp với cha (Tỵ - Dậu)\",\"✅ Tam hợp với mẹ (Tỵ - Dậu)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-21 09:14:04'),
(11, 5, 1976, 1990, 2028, 6, 'Tốt', '{\"familyUsefulGod\":\"Mộc\",\"bestYear\":{\"year\":2028,\"bazi\":{\"year\":{\"can\":\"Nhâm\",\"chi\":\"Tý\"},\"elements\":{\"Mộc\":1},\"yearChi\":\"Tý\",\"yearCan\":\"Nhâm\",\"menh\":\"Mộc\"},\"score\":6,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\",\"✅ Tam hợp với cha (Thân - Tý)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-21 09:51:29'),
(12, 5, 1985, 2011, 2025, 5, 'Tốt', '{\"familyUsefulGod\":\"Kim\",\"bestYear\":{\"year\":2025,\"bazi\":{\"year\":{\"can\":\"Kỷ\",\"chi\":\"Dậu\"},\"elements\":{\"Thổ\":1},\"yearChi\":\"Dậu\",\"yearCan\":\"Kỷ\",\"menh\":\"Thổ\"},\"score\":5,\"details\":[\"✅ Con sinh Dụng thần gia đình\",\"✅ Tam hợp với cha (Tỵ - Dậu)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-21 09:59:35'),
(13, 5, 2012, 1990, 2024, 5, 'Tốt', '{\"familyUsefulGod\":\"Kim\",\"bestYear\":{\"year\":2024,\"bazi\":{\"year\":{\"can\":\"Mậu\",\"chi\":\"Thân\"},\"elements\":{\"Thổ\":1},\"yearChi\":\"Thân\",\"yearCan\":\"Mậu\",\"menh\":\"Thổ\"},\"score\":5,\"details\":[\"✅ Con sinh Dụng thần gia đình\",\"✅ Tam hợp với cha (Thân - Thân)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-21 10:59:50'),
(14, 5, 2012, 1990, 2028, 6, 'Tốt', '{\"familyUsefulGod\":\"Mộc\",\"bestYear\":{\"year\":2028,\"bazi\":{\"year\":{\"can\":\"Nhâm\",\"chi\":\"Tý\"},\"elements\":{\"Mộc\":1},\"yearChi\":\"Tý\",\"yearCan\":\"Nhâm\",\"menh\":\"Mộc\"},\"score\":6,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\",\"✅ Tam hợp với cha (Thân - Tý)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-21 12:57:05'),
(15, 7, 1997, 2004, 2024, 4, 'Trung bình', '{\"familyUsefulGod\":\"Thổ\",\"bestYear\":{\"year\":2024,\"bazi\":{\"year\":{\"can\":\"Mậu\",\"chi\":\"Thân\"},\"elements\":{\"Thổ\":1},\"yearChi\":\"Thân\",\"yearCan\":\"Mậu\",\"menh\":\"Thổ\"},\"score\":4,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\",\"✅ Tam hợp với mẹ (Tý - Thân)\",\"❌ Tứ xung với cha (Tỵ - Thân)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-22 06:52:26'),
(16, 7, 1997, 2004, 2029, 8, 'Rất tốt', '{\"familyUsefulGod\":\"Mộc\",\"bestYear\":{\"year\":2029,\"bazi\":{\"year\":{\"can\":\"Quý\",\"chi\":\"Sửu\"},\"elements\":{\"Mộc\":1},\"yearChi\":\"Sửu\",\"yearCan\":\"Quý\",\"menh\":\"Mộc\"},\"score\":8,\"details\":[\"✅ Con có hành trùng Dụng thần gia đình\",\"✅ Tam hợp với cha (Tỵ - Sửu)\"]},\"totalYears\":7,\"startYear\":2024,\"endYear\":2030}', '2025-11-22 06:52:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history_xaynha`
--

CREATE TABLE `history_xaynha` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `owner_year` int(11) NOT NULL,
  `build_year` int(11) NOT NULL,
  `kimlau` tinyint(1) DEFAULT 0,
  `hoangoc` tinyint(1) DEFAULT 0,
  `tamtai` tinyint(1) DEFAULT 0,
  `evaluation` varchar(255) DEFAULT NULL,
  `detail` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history_xaynha`
--

INSERT INTO `history_xaynha` (`id`, `user_id`, `owner_year`, `build_year`, `kimlau`, `hoangoc`, `tamtai`, `evaluation`, `detail`, `created_at`) VALUES
(1, 1, 1990, 2026, 0, 0, 0, 'Năm xây nhà rất tốt', 'Không phạm Kim Lâu, Hoang Ốc, Tam Tai. Thích hợp khởi công, động thổ.', '2025-11-19 07:53:36'),
(2, NULL, 1985, 2025, 1, 0, 0, 'Năm xây nhà phạm Kim Lâu', 'Nên cân nhắc mượn tuổi hoặc chuyển sang năm khác để tránh xui rủi.', '2025-11-19 07:53:36'),
(4, 5, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-20 17:36:33'),
(7, 5, 1975, 2005, 0, 0, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Ất\",\"chiBuild\":\"Dậu\",\"tuoiOwner\":30,\"score\":4,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Hoang Ốc\",\"✅ Không phạm Tam Tai\"],\"warnings\":[]}', '2025-11-20 17:36:42'),
(8, 5, 1975, 2020, 0, 0, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Canh\",\"chiBuild\":\"Tý\",\"tuoiOwner\":45,\"score\":4,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Hoang Ốc\",\"✅ Không phạm Tam Tai\"],\"warnings\":[]}', '2025-11-20 17:36:46'),
(9, 5, 1975, 2020, 0, 0, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Canh\",\"chiBuild\":\"Tý\",\"tuoiOwner\":45,\"score\":4,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Hoang Ốc\",\"✅ Không phạm Tam Tai\"],\"warnings\":[]}', '2025-11-20 17:36:51'),
(10, 5, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-20 17:36:56'),
(11, 5, 1975, 2009, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Kỷ\",\"chiBuild\":\"Sửu\",\"tuoiOwner\":34,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-20 17:37:09'),
(12, 5, 1975, 2030, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Canh\",\"chiBuild\":\"Tuất\",\"tuoiOwner\":55,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-20 17:37:18'),
(13, 5, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-20 19:28:05'),
(14, 5, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-20 19:39:10'),
(15, 5, 1997, 2026, 0, 0, 0, 'NÊN LÀM', '{\"canOwner\":\"Đinh\",\"chiOwner\":\"Sửu\",\"canBuild\":\"Bính\",\"chiBuild\":\"Ngọ\",\"tuoiOwner\":29,\"score\":4,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Hoang Ốc\",\"✅ Không phạm Tam Tai\"],\"warnings\":[]}', '2025-11-20 20:25:42'),
(16, 5, 1997, 2009, 0, 0, 0, 'NÊN LÀM', '{\"canOwner\":\"Đinh\",\"chiOwner\":\"Sửu\",\"canBuild\":\"Kỷ\",\"chiBuild\":\"Sửu\",\"tuoiOwner\":12,\"score\":4,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Hoang Ốc\",\"✅ Không phạm Tam Tai\"],\"warnings\":[]}', '2025-11-21 09:15:25'),
(17, 5, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-21 10:53:38'),
(18, 5, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-21 10:53:42'),
(19, 5, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-21 12:54:05'),
(20, 5, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-21 18:59:49'),
(21, 5, 1975, 2026, 1, 0, 0, 'CÂN NHẮC', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Bính\",\"chiBuild\":\"Ngọ\",\"tuoiOwner\":51,\"score\":0,\"details\":[\"✅ Không phạm Hoang Ốc\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ PHẠM KIM LÂU: Tránh làm nhà\"]}', '2025-11-21 18:59:59'),
(22, 5, 1973, 2026, 1, 0, 0, 'CÂN NHẮC', '{\"canOwner\":\"Quý\",\"chiOwner\":\"Sửu\",\"canBuild\":\"Bính\",\"chiBuild\":\"Ngọ\",\"tuoiOwner\":53,\"score\":0,\"details\":[\"✅ Không phạm Hoang Ốc\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ PHẠM KIM LÂU: Tránh làm nhà\"]}', '2025-11-21 19:00:09'),
(24, 5, 1973, 2027, 0, 0, 0, 'NÊN LÀM', '{\"canOwner\":\"Quý\",\"chiOwner\":\"Sửu\",\"canBuild\":\"Đinh\",\"chiBuild\":\"Mùi\",\"tuoiOwner\":54,\"score\":4,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Hoang Ốc\",\"✅ Không phạm Tam Tai\"],\"warnings\":[]}', '2025-11-21 19:01:05'),
(25, 7, 1975, 2024, 0, 1, 0, 'NÊN LÀM', '{\"canOwner\":\"Ất\",\"chiOwner\":\"Mão\",\"canBuild\":\"Giáp\",\"chiBuild\":\"Thìn\",\"tuoiOwner\":49,\"score\":2,\"details\":[\"✅ Không phạm Kim Lâu\",\"✅ Không phạm Tam Tai\"],\"warnings\":[\"❌ Phạm Hoang Ốc: Nhà dễ vắng vẻ\"]}', '2025-11-22 06:53:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history_xemngay`
--

CREATE TABLE `history_xemngay` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `query_date` date NOT NULL,
  `lunar_date` varchar(50) DEFAULT NULL,
  `can_chi_day` varchar(100) DEFAULT NULL,
  `rating` enum('tot','xau','binh_thuong') DEFAULT 'binh_thuong',
  `work_good` text DEFAULT NULL,
  `work_bad` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history_xemngay`
--

INSERT INTO `history_xemngay` (`id`, `user_id`, `query_date`, `lunar_date`, `can_chi_day`, `rating`, `work_good`, `work_bad`, `note`, `created_at`) VALUES
(1, 1, '2025-01-01', '01/12 Giáp Thìn', 'Ngày Giáp Tý', 'tot', 'Khai trương, xuất hành, ký kết hợp đồng', 'Động thổ, sửa chữa lớn', 'Ngày đầu năm tốt, nhiều cát khí.', '2025-11-19 07:53:36'),
(2, NULL, '2025-02-14', '16/01 Ất Tỵ', 'Ngày Ất Sửu', 'binh_thuong', 'Gặp gỡ, hẹn hò, cầu nguyện', 'Kinh doanh lớn, tranh tụng', 'Ngày bình thường, chú ý lời ăn tiếng nói.', '2025-11-19 07:53:36'),
(3, 5, '2025-11-19', '30-09-2025', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-19 17:54:09'),
(4, 5, '2025-11-23', '04-10-2025', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-19 18:41:11'),
(5, 5, '2025-11-10', '21-09-2025', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-19 18:52:28'),
(6, 5, '2027-11-19', '22-10-2027', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-19 19:01:49'),
(7, 5, '2024-01-15', '15-12-2023', NULL, 'tot', NULL, NULL, 'Ngày tốt - Test', '2025-11-19 19:19:31'),
(8, 5, '2030-11-19', '24-10-2030', NULL, 'binh_thuong', NULL, NULL, 'Ngày bình thường', '2025-11-19 19:19:54'),
(9, 5, '2004-09-05', '21-07-2004', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-19 19:41:21'),
(10, 5, '2007-09-11', '2007-08-01', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-19 19:41:57'),
(11, 5, '2088-11-19', '07-10-2088', NULL, 'tot', NULL, NULL, 'Ngày tốt (cát lợi)', '2025-11-19 19:42:22'),
(12, 5, '2025-06-25', '2025-06-01', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-19 19:50:12'),
(13, 5, '2025-11-20', '3/12/2025', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-20 06:34:47'),
(14, 5, '2025-09-22', '2025-08-01', NULL, 'binh_thuong', NULL, NULL, 'Ngày bình thường', '2025-11-20 06:35:16'),
(15, 5, '2025-11-04', '16/12/2025', NULL, 'binh_thuong', NULL, NULL, 'Ngày bình thường', '2025-11-20 06:45:00'),
(16, 5, '2025-10-20', '29-08-2025', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-20 07:50:28'),
(17, 5, '2025-11-18', '2025-09-29', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-20 07:50:35'),
(18, 5, '2025-11-30', '13/12/2025', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-20 08:23:55'),
(19, 5, '2025-01-01', 'Sinh con: Cha 1985 - Mẹ 1990', NULL, 'binh_thuong', NULL, NULL, 'Điểm: 6/10 - Dụng thần: Kim', '2025-11-20 10:50:12'),
(20, 5, '2028-01-01', 'Sinh con: Cha 1985 - Mẹ 1990', NULL, 'binh_thuong', NULL, NULL, 'Điểm: 4/10 - Dụng thần: Mộc', '2025-11-20 10:50:52'),
(21, 5, '1997-01-13', '5-12-1996', NULL, 'binh_thuong', NULL, NULL, 'Ngày sinh: 1997-01-13 - Giản Hạ Thủy - Ma Kết', '2025-11-20 13:42:13'),
(22, 5, '2029-01-01', 'Sinh con: Cha 1997 - Mẹ 2004', NULL, 'binh_thuong', NULL, NULL, 'Điểm: 8/10 - Dụng thần: Mộc', '2025-11-20 13:44:04'),
(23, 5, '2019-11-20', '24-10-2019', NULL, 'binh_thuong', NULL, NULL, 'Ngày bình thường', '2025-11-20 15:35:28'),
(24, 5, '2025-11-03', '15/12/2025', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-20 15:37:21'),
(25, 5, '2024-01-01', 'Xây nhà 1975 → 2024', NULL, 'binh_thuong', NULL, NULL, 'Xây nhà: NÊN LÀM - Điểm: 2', '2025-11-20 17:17:45'),
(26, 5, '2026-01-01', 'Xây nhà 1997 → 2026', NULL, 'binh_thuong', NULL, NULL, 'Xây nhà: NÊN LÀM - Điểm: 4', '2025-11-20 20:25:45'),
(27, 5, '2025-11-21', '02-10-2025', NULL, 'binh_thuong', NULL, NULL, 'Ngày bình thường', '2025-11-21 07:10:13'),
(28, 5, '2025-10-22', '2025-09-02', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-21 07:17:27'),
(29, 5, '2004-05-09', '21-03-2004', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-21 07:18:11'),
(30, 5, '1997-01-23', '1996-12-15', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-21 07:18:42'),
(31, 5, '2003-11-21', '28-10-2003', NULL, 'binh_thuong', NULL, NULL, 'Ngày sinh: 2003-11-21 - Dương Liễu Mộc - Bọ Cạp', '2025-11-21 09:12:55'),
(32, 5, '2025-11-13', '25/12', NULL, 'binh_thuong', NULL, NULL, 'Ngày 13-11-2025 (Âm: 25/12) - Ngày bình thường - Can Chi: Bính Tuất - Điểm: 3.1/10', '2025-11-21 15:47:55'),
(33, 5, '2025-11-01', '13/12', NULL, 'xau', NULL, NULL, 'Ngày 01-11-2025 (Âm: 13/12) - Ngày xấu (bất lợi) - Can Chi: Giáp Tuất - Điểm: 1.4/10', '2025-11-21 15:48:37'),
(34, 5, '2027-01-01', 'Xây nhà 1973 → 2027', NULL, 'binh_thuong', NULL, NULL, 'Xây nhà: NÊN LÀM - Điểm: 4', '2025-11-21 19:01:07'),
(35, 5, '2025-08-23', '2025-07-01', NULL, 'binh_thuong', NULL, NULL, 'Ngày bình thường', '2025-11-21 19:28:03'),
(36, 5, '2025-11-14', '25/9', NULL, 'binh_thuong', NULL, NULL, 'bad', '2025-11-21 19:53:08'),
(37, 5, '2034-11-21', '11-10-2034', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-21 20:13:32'),
(38, 7, '2025-11-15', '25/9', NULL, 'xau', NULL, NULL, 'Ngày 15-11-2025 (Âm: 25/9) - Ngày xấu (bất lợi) - Can Chi: Mậu Tý - Điểm: 0.1/10', '2025-11-22 06:47:41'),
(39, 7, '2025-11-22', '03-10-2025', NULL, 'tot', NULL, NULL, 'Ngày tốt (cát lợi)', '2025-11-22 06:48:10'),
(40, 7, '2025-10-23', '2025-09-03', NULL, 'xau', NULL, NULL, 'Ngày xấu (bất lợi)', '2025-11-22 06:48:18'),
(41, 7, '1997-01-13', '05-12-1996', NULL, 'tot', NULL, NULL, 'Ngày tốt (cát lợi)', '2025-11-22 06:49:51'),
(42, 7, '2004-09-05', '21-7-2004', NULL, 'binh_thuong', NULL, NULL, 'Ngày sinh: 2004-09-05 - Tuyền Trung Thủy - Xử Nữ', '2025-11-22 06:50:40'),
(43, 7, '1997-11-24', '25-10-1997', NULL, 'binh_thuong', NULL, NULL, 'Ngày sinh: 1997-11-24 - Giản Hạ Thủy - Nhân Mã', '2025-11-22 07:00:18'),
(44, 7, '2026-11-22', '14-10-2026', NULL, 'binh_thuong', NULL, NULL, 'Ngày sinh: 2026-11-22 - Thiên Hà Thủy - Bọ Cạp', '2025-11-22 07:08:55'),
(45, 7, '2025-11-23', '4/10', NULL, 'tot', NULL, NULL, 'Ngày 23-11-2025 (Âm: 4/10) - Ngày tốt (cát lợi) - Can Chi: Bính Thân - Điểm: 7.4/10', '2025-11-22 08:36:39'),
(46, 7, '2025-11-21', '2/10', NULL, 'binh_thuong', NULL, NULL, 'Ngày 21-11-2025 (Âm: 2/10) - Ngày bình thường - Can Chi: Giáp Ngọ - Điểm: 4.4/10', '2025-11-22 08:37:29'),
(47, 5, '2025-11-22', '3/10', NULL, 'tot', NULL, NULL, 'Ngày 22-11-2025 (Âm: 3/10) - Ngày tốt (cát lợi) - Can Chi: Ất Mùi - Điểm: 8.9/10', '2025-11-22 09:43:45'),
(48, 5, '2025-11-16', '26/9', NULL, 'binh_thuong', NULL, NULL, 'Ngày 16-11-2025 (Âm: 26/9) - Ngày bình thường - Can Chi: Kỷ Sửu - Điểm: 6.2/10', '2025-11-22 10:44:33'),
(49, 6, '2025-11-22', '03-10-2025', NULL, 'tot', NULL, NULL, 'Ngày tốt (cát lợi)', '2025-11-22 19:10:31'),
(50, 6, '2025-11-07', '17/9', NULL, 'tot', NULL, NULL, 'Ngày 07-11-2025 (Âm: 17/9) - Ngày tốt (cát lợi) - Can Chi: Canh Thìn - Điểm: 7.2/10', '2025-11-23 06:36:17'),
(51, 6, '2026-09-25', '2026-08-15', NULL, 'tot', NULL, NULL, 'Ngày tốt (cát lợi)', '2025-11-23 08:38:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('national','religious','traditional','other') NOT NULL DEFAULT 'traditional',
  `is_lunar` tinyint(1) NOT NULL DEFAULT 1,
  `lunar_month` int(2) DEFAULT NULL,
  `lunar_day` int(2) DEFAULT NULL,
  `solar_date` date DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `solar_day` int(11) DEFAULT NULL,
  `solar_month` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `holidays`
--

INSERT INTO `holidays` (`id`, `name`, `description`, `type`, `is_lunar`, `lunar_month`, `lunar_day`, `solar_date`, `is_recurring`, `created_at`, `updated_at`, `is_active`, `solar_day`, `solar_month`) VALUES
(2, 'Tết Nguyên Tiêu (Rằm tháng Giêng)', 'Lễ hội đèn hoa, còn gọi là Rằm tháng Giêng', 'traditional', 1, 1, 15, NULL, 1, '2025-11-22 19:59:17', '2025-11-22 19:59:17', 1, NULL, NULL),
(4, 'Phật Đản', 'Ngày đức Phật Thích Ca Mâu Ni đản sinh', 'religious', 1, 4, 15, NULL, 1, '2025-11-22 19:59:17', '2025-11-22 19:59:17', 1, NULL, NULL),
(5, 'Tết Dương lịch', 'Năm mới dương lịch', 'national', 0, NULL, NULL, '2025-01-01', 1, '2025-11-22 19:59:17', '2025-11-23 15:16:51', 1, 1, 1),
(6, 'Ngày Giải phóng Miền Nam', 'Ngày thống nhất đất nước', 'national', 0, NULL, NULL, '2025-04-30', 1, '2025-11-22 19:59:17', '2025-11-23 15:16:51', 1, 30, 4),
(7, 'Ngày Quốc tế Lao động', 'Ngày Quốc tế Lao động', 'national', 0, NULL, NULL, '2025-05-01', 1, '2025-11-22 19:59:17', '2025-11-23 15:16:51', 1, 1, 5),
(8, 'Quốc khánh', 'Ngày Quốc khánh nước Cộng hòa Xã hội Chủ nghĩa Việt Nam', 'national', 0, NULL, NULL, '2025-09-02', 1, '2025-11-22 19:59:17', '2025-11-23 15:16:51', 1, 2, 9),
(9, 'Ngày Nhà giáo Việt Nam', 'Ngày tôn vinh các nhà giáo Việt Nam', 'other', 0, NULL, NULL, '2025-11-20', 1, '2025-11-22 19:59:17', '2025-11-23 15:16:51', 1, 20, 11),
(10, 'Ngày Quốc tế Phụ nữ', 'Ngày Quốc tế Phụ nữ', 'other', 0, NULL, NULL, '2025-03-08', 1, '2025-11-22 19:59:17', '2025-11-23 15:16:51', 1, 8, 3),
(11, 'Tết Trung Thu', 'Tết thiếu nhi, Rằm tháng Tám', 'traditional', 1, 8, 15, NULL, 1, '2025-11-22 19:59:17', '2025-11-22 19:59:17', 1, NULL, NULL),
(12, 'Lễ Vu Lan', 'Ngày báo hiếu cha mẹ', 'religious', 1, 7, 15, NULL, 1, '2025-11-22 19:59:17', '2025-11-22 19:59:17', 1, NULL, NULL),
(14, 'Ngày Giải phóng Miền Nam', NULL, 'national', 0, NULL, NULL, '2025-04-30', 1, '2025-11-23 04:39:46', '2025-11-23 15:16:51', 1, 30, 4),
(15, 'Quốc tế Lao động', NULL, 'national', 0, NULL, NULL, '2025-05-01', 1, '2025-11-23 04:39:46', '2025-11-23 04:39:46', 1, NULL, NULL),
(16, 'Quốc khánh Việt Nam', NULL, 'national', 0, NULL, NULL, '2025-09-02', 1, '2025-11-23 04:39:46', '2025-11-23 04:39:46', 1, NULL, NULL),
(17, 'Tết Nguyên Đán', NULL, 'traditional', 1, 1, 1, NULL, 1, '2025-11-23 04:39:46', '2025-11-23 04:39:46', 1, NULL, NULL),
(18, 'Giỗ Tổ Hùng Vương', NULL, 'traditional', 1, 3, 10, NULL, 1, '2025-11-23 04:39:46', '2025-11-23 04:39:46', 1, NULL, NULL),
(19, 'Tết Đoan Ngọ', NULL, 'traditional', 1, 5, 5, NULL, 1, '2025-11-23 04:39:46', '2025-11-23 04:39:46', 1, NULL, NULL),
(20, 'Rằm tháng 7', NULL, 'religious', 1, 7, 15, NULL, 1, '2025-11-23 04:39:46', '2025-11-23 04:39:46', 1, NULL, NULL),
(23, 'Quốc tế Lao động', NULL, 'national', 0, NULL, NULL, '2025-05-01', 1, '2025-11-23 04:45:06', '2025-11-23 04:45:06', 1, NULL, NULL),
(24, 'Quốc khánh Việt Nam', NULL, 'national', 0, NULL, NULL, '2025-09-02', 1, '2025-11-23 04:45:06', '2025-11-23 04:45:06', 1, NULL, NULL),
(28, 'Rằm tháng 7', NULL, 'religious', 1, 7, 15, NULL, 1, '2025-11-23 04:45:06', '2025-11-23 04:45:06', 1, NULL, NULL),
(31, 'valentine', 'msavsfdvsfgv', 'other', 0, NULL, NULL, NULL, 1, '2025-11-23 22:02:48', '2025-11-23 22:02:48', 1, 14, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_events`
--

CREATE TABLE `system_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `event_type` enum('community','promotion','system_update','announcement','other') NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `published_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `system_events`
--

INSERT INTO `system_events` (`id`, `title`, `description`, `content`, `event_type`, `status`, `start_date`, `end_date`, `location`, `image_url`, `is_featured`, `created_by`, `created_at`, `updated_at`, `published_at`) VALUES
(1, 'đua xe lăn', 'lăn xe tốc độ cao', 'lăn xe tốc độ cao', 'community', 'published', '2025-12-04', '2025-12-04', 'hà nội', '', 1, 6, '2025-11-22 23:54:05', '2025-11-23 15:08:43', '2025-11-23 09:08:43'),
(3, 'ăn thịt bé trai', '', '', 'community', 'published', '2025-11-22', '0000-00-00', '', '', 1, 6, '2025-11-23 03:32:29', '2025-11-23 03:32:29', '2025-11-22 21:32:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `status`, `updated_at`, `created_at`) VALUES
(1, 'Trần Quỳnh Nhi', 'nhi@example.com', '$2y$10$3U5UdfMumehbctpEVkI8quMsfJX3NQFfxrvhUzo2..j6GlI1Pzo3y', 'user', 1, '2025-11-22 20:15:56', '2025-11-19 07:53:36'),
(5, 'LZ', 'zcj@gmail.com', '$2y$10$PhHo4BfHafsqS3HMICKvtOuxKHNHUgl175Evaybc1kg4hr93U2Lqe', 'user', 1, NULL, '2025-11-19 10:02:07'),
(6, 'hehehe', 'trqanchautran@gmail.com', '$2y$10$OtYlYhrkpDihjDy49RTbjuoZBs/QJ89dd1DZOhGw49OjN35nRLkQ6', 'admin', 1, NULL, '2025-11-19 10:12:20'),
(7, 'eeee', 'GRRGRGR@gmail.com', '$2y$10$DuLYwBYRdoYXYkF9.uPhFeNjoYF.ZZwJ6ZhA./eCnUUAoFRk4zsJa', 'user', 1, NULL, '2025-11-21 15:07:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_events`
--

CREATE TABLE `user_events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#3498db',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_events`
--

INSERT INTO `user_events` (`id`, `user_id`, `title`, `event_date`, `event_time`, `description`, `color`, `created_at`, `updated_at`) VALUES
(3, 7, 'sinh nhật', '2025-11-20', '11:11:00', 'nhj', '#420b0b', '2025-11-22 15:36:02', '2025-11-22 15:36:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_profiles`
--

CREATE TABLE `user_profiles` (
  `user_id` int(11) NOT NULL,
  `gender` enum('nam','nu','khac') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_profiles`
--

INSERT INTO `user_profiles` (`user_id`, `gender`, `dob`, `phone`, `address`, `avatar_url`, `bio`, `updated_at`) VALUES
(1, 'nu', '2003-05-10', '0987654321', 'Hà Nội', NULL, 'Admin hệ thống Lịch Việt', '2025-11-19 07:53:36'),
(5, 'nam', '2004-09-05', 'anh hiên ơi', NULL, NULL, NULL, '2025-11-22 10:16:14'),
(6, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-19 16:12:21'),
(7, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-21 21:07:32');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fav` (`user_id`,`solar_date`);

--
-- Chỉ mục cho bảng `history_chuyenngay`
--
ALTER TABLE `history_chuyenngay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_chuyenngay_user` (`user_id`);

--
-- Chỉ mục cho bảng `history_huongnha`
--
ALTER TABLE `history_huongnha`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_huongnha_user` (`user_id`);

--
-- Chỉ mục cho bảng `history_kethon`
--
ALTER TABLE `history_kethon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_kethon_user` (`user_id`);

--
-- Chỉ mục cho bảng `history_laman`
--
ALTER TABLE `history_laman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_laman_user` (`user_id`);

--
-- Chỉ mục cho bảng `history_ngaysinh`
--
ALTER TABLE `history_ngaysinh`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_ngaysinh_user` (`user_id`);

--
-- Chỉ mục cho bảng `history_sinhcon`
--
ALTER TABLE `history_sinhcon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_sinhcon_user` (`user_id`);

--
-- Chỉ mục cho bảng `history_xaynha`
--
ALTER TABLE `history_xaynha`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_xaynha_user` (`user_id`);

--
-- Chỉ mục cho bảng `history_xemngay`
--
ALTER TABLE `history_xemngay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_history_xemngay_user` (`user_id`);

--
-- Chỉ mục cho bảng `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_lunar` (`lunar_month`,`lunar_day`),
  ADD KEY `idx_solar` (`solar_date`),
  ADD KEY `idx_created` (`created_at`);

--
-- Chỉ mục cho bảng `system_events`
--
ALTER TABLE `system_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `user_events`
--
ALTER TABLE `user_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_date` (`event_date`);

--
-- Chỉ mục cho bảng `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT cho bảng `history_chuyenngay`
--
ALTER TABLE `history_chuyenngay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT cho bảng `history_huongnha`
--
ALTER TABLE `history_huongnha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `history_kethon`
--
ALTER TABLE `history_kethon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `history_laman`
--
ALTER TABLE `history_laman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `history_ngaysinh`
--
ALTER TABLE `history_ngaysinh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `history_sinhcon`
--
ALTER TABLE `history_sinhcon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `history_xaynha`
--
ALTER TABLE `history_xaynha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `history_xemngay`
--
ALTER TABLE `history_xemngay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT cho bảng `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `system_events`
--
ALTER TABLE `system_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `user_events`
--
ALTER TABLE `user_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `history_chuyenngay`
--
ALTER TABLE `history_chuyenngay`
  ADD CONSTRAINT `fk_history_chuyenngay_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `history_huongnha`
--
ALTER TABLE `history_huongnha`
  ADD CONSTRAINT `fk_history_huongnha_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `history_kethon`
--
ALTER TABLE `history_kethon`
  ADD CONSTRAINT `fk_history_kethon_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `history_laman`
--
ALTER TABLE `history_laman`
  ADD CONSTRAINT `fk_history_laman_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `history_ngaysinh`
--
ALTER TABLE `history_ngaysinh`
  ADD CONSTRAINT `fk_history_ngaysinh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `history_sinhcon`
--
ALTER TABLE `history_sinhcon`
  ADD CONSTRAINT `fk_history_sinhcon_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `history_xaynha`
--
ALTER TABLE `history_xaynha`
  ADD CONSTRAINT `fk_history_xaynha_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `history_xemngay`
--
ALTER TABLE `history_xemngay`
  ADD CONSTRAINT `fk_history_xemngay_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `system_events`
--
ALTER TABLE `system_events`
  ADD CONSTRAINT `system_events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `user_events`
--
ALTER TABLE `user_events`
  ADD CONSTRAINT `fk_user_events_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
