# 🎓 ROOM Manager • ระบบจัดการข้อมูลนักศึกษา (PHP MySQL CRUD)

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

ระบบจัดการข้อมูลนักศึกษาแบบ **CRUD (Create, Read, Update, Delete)** สำหรับผู้เริ่มต้น พัฒนาด้วย **PHP & MySQL (Prepared Statements)** ในไฟล์เดียว เน้นความเข้าใจง่ายและสถาปัตยกรรมที่ปลอดภัยของระบบ

---

## 🖼️ ตัวอย่างหน้าจอระบบ (Preview)

![ROOM Manager Preview](images/preview.png)

---

## 🚀 จุดเด่นของระบบ

- **CRUD ครบจบในไฟล์เดียว:** เพิ่ม (Create), แสดงผล (Read), แก้ไข (Update) และลบ (Delete) ข้อมูลในไฟล์ `index.php`
- **ปลอดภัยจาก SQL Injection:** ทุกคำสั่ง SQL ใช้ **Prepared Statements** (`$conn->prepare()`) ในการรับส่งข้อมูล
- **ป้องกัน Form Resubmission (PRG Pattern):** หลังจากบันทึกหรือลบข้อมูลเสร็จ ระบบจะทำการ Redirect เพื่อป้องกันปัญหากด Refresh แล้วข้อมูลถูกบันทึกซ้ำ
- **แก้ไขข้อมูลในตารางได้ทันที (Inline Editing):** สามารถพิมพ์แก้ไขชื่อและสาขาวิชาในตาราง แล้วกดปุ่ม `อัปเดต` เพื่อบันทึกได้ทันที

---

## ⚙️ โครงสร้างการทำงาน (CRUD Workflow)

| คำสั่ง (CRUD)      | SQL ที่ใช้                                            | การทำงานของระบบ                                                         |
| :----------------- | :---------------------------------------------------- | :---------------------------------------------------------------------- |
| **Create (เพิ่ม)** | `INSERT INTO student (name, major) VALUES (?, ?)`     | รับค่าจากฟอร์ม ตรวจสอบและตัดช่องว่างด้วย `trim()` แล้วบันทึกลงฐานข้อมูล |
| **Read (แสดงผล)**  | `SELECT * FROM student ORDER BY id DESC`              | ดึงข้อมูลนักศึกษาทั้งหมดมาแสดงในตาราง โดยเรียงจากรายการล่าสุดอยู่บนสุด  |
| **Update (แก้ไข)** | `UPDATE student SET name = ?, major = ? WHERE id = ?` | รับค่าที่แก้ไขจากตาราง และอัปเดตข้อมูลลงฐานข้อมูลตาม ID ของรายการนั้น   |
| **Delete (ลบ)**    | `DELETE FROM student WHERE id = ?`                    | ลบข้อมูลตาม ID หลังจากผู้ใช้กดยืนยัน (Confirm)                          |

---

## 🛠️ การติดตั้งและใช้งาน (สำหรับผู้เริ่มต้น)

### 1. สิ่งที่ต้องมี

- โปรแกรมจำลองเซิร์ฟเวอร์ เช่น **XAMPP**, **MAMP** หรือ **Laragon** (รองรับ PHP 7.4 ขึ้นไป และ MySQL)

### 2. สร้างฐานข้อมูล

1. เปิด **phpMyAdmin** (เช่น `http://localhost/phpmyadmin`)
2. คัดลอกและรันคำสั่ง SQL ด้านล่างเพื่อสร้างฐานข้อมูล `room` และตาราง `student`:

```sql
-- 1. สร้างฐานข้อมูล room
CREATE DATABASE IF NOT EXISTS `room` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `room`;

-- 2. สร้างตาราง student
CREATE TABLE IF NOT EXISTS `student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `major` varchar(255) NOT NULL COMMENT 'สาขาวิชา',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'เวลาที่บันทึก',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. เพิ่มข้อมูลตัวอย่าง
INSERT INTO `student` (`name`, `major`) VALUES
('Jiramate', 'IT'),
('Somchai', 'SCI');
```

### 3. ตั้งค่าการเชื่อมต่อฐานข้อมูล

ในไฟล์ `index.php` (บรรทัดที่ 3) สามารถตรวจสอบและตั้งค่าการเชื่อมต่อได้ดังนี้:

```php
// รูปแบบ: new mysqli("เซิร์ฟเวอร์", "ชื่อผู้ใช้", "รหัสผ่าน", "ชื่อฐานข้อมูล")
$conn = new mysqli("localhost", "root", "", "room");
```

_(กรณีใช้ XAMPP โดยทั่วไปชื่อผู้ใช้คือ `root` และรหัสผ่านว่างไว้ `""` ตามตัวอย่างได้เลย)_

### 4. เปิดใช้งานระบบ

นำโฟลเดอร์โปรเจกต์ (`CRUID-Basic`) ไปวางในโฟลเดอร์ `htdocs` (สำหรับ XAMPP) แล้วเปิดเบราว์เซอร์ไปที่:
`http://localhost/CRUID-Basic`

---

## 💡 สำหรับผู้เริ่มต้นศึกษาโค้ด

ภายในไฟล์ `index.php` มีคำอธิบายการทำงานของโค้ดแต่ละส่วนไว้อย่างละเอียด ทั้งการเชื่อมต่อฐานข้อมูล กลไก Prepared Statements และเทคนิคการจัดการฟอร์มใน PHP สามารถเปิดโค้ดอ่านและเรียนรู้ทีละบรรทัดได้ทันที

---

## 📌 ไอเดียพัฒนาต่อยอด

- เพิ่มช่องค้นหาข้อมูลนักศึกษา (Search & Filter)
- เพิ่มระบบแบ่งหน้า (Pagination) เมื่อมีข้อมูลจำนวนมาก
- เพิ่มการตรวจสอบความถูกต้องของข้อมูล (Validation) เช่น ห้ามกรอกตัวเลขในช่องชื่อ-นามสกุล
