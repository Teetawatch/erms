# 📋 Project Brief: ระบบจัดการบันทึกการทำงานพนักงาน (Final)

## Overview

ต้องการพัฒนาระบบจัดการบันทึกการทำงานพนักงานรายบุคคล สำหรับองค์กรเดียว (Single-tenant) ทีมงานไม่เกิน 10 คน รองรับการอัปเดตข้อมูลแบบ **Polling ทุก 15 วินาที** (ไม่ใช้ WebSocket เพื่อให้ deploy บน Shared Hosting ได้)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 |
| Frontend | Livewire 3 + Alpine.js + Tailwind CSS + Flowbite |
| Real-time (Polling) | Livewire `wire:poll.15s` |
| Auth & UI Starter | Laravel Breeze |
| Role & Permission | Spatie Laravel Permission |
| Audit Log | Spatie Laravel Activity Log |
| Export | Laravel Excel + DomPDF |
| File Storage | Laravel Storage (local disk) |
| Calendar UI | FullCalendar.js |
| Database | MySQL |

> ⚠️ **ไม่ใช้ Laravel Reverb / WebSocket** เพราะ Shared Hosting ไม่รองรับ ใช้ Livewire Polling แทน ซึ่งเพียงพอสำหรับทีมขนาดไม่เกิน 10 คน

---

## Hosting

- **ประเภท:** Shared Hosting (Linux, PHP 8.3+, MySQL)
- **ความต้องการขั้นต่ำ:** PHP 8.3, MySQL 8, SSH Access, Composer, SSL
- **ไม่ต้องการ:** Redis, Supervisor, WebSocket port

---

## UX/UI Design Guidelines

### Design Direction: "Clean Professional Dark"
ใช้ Dark Theme เป็นหลัก โทนสีเข้ม อ่านง่าย ไม่เครียดสายตา เหมาะกับการใช้งานนาน ดูเป็นมืออาชีพ สไตล์ใกล้เคียง Linear / Notion / Vercel Dashboard

---

### Color Palette

| ชื่อ | Hex | ใช้สำหรับ |
|---|---|---|
| Background | `#0d0f14` | พื้นหลังหลัก |
| Surface | `#151820` | Sidebar, Card |
| Surface 2 | `#1c2030` | Hover, Input |
| Border | `#252a38` | เส้นแบ่ง |
| Accent Blue | `#4f8ef7` | Primary action, Link |
| Accent Purple | `#7c5cfc` | Gradient, Badge |
| Green | `#22d3a0` | Success, Done |
| Orange | `#f97316` | Warning, In Progress |
| Red | `#f43f5e` | Urgent, Error |
| Yellow | `#fbbf24` | Medium priority |
| Text | `#e8eaf0` | ข้อความหลัก |
| Muted | `#6b7280` | Label, Placeholder |

---

### Typography

| ใช้สำหรับ | Font | น้ำหนัก |
|---|---|---|
| Heading, Logo | Syne | 700–800 |
| Body, UI ทั่วไป | DM Sans | 300–500 |

```html
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
```

---

### Layout & Responsive

```
Desktop (≥1024px)
├── Sidebar fixed ซ้าย 240px
└── Main content ขวา เต็ม

Tablet (768px–1023px)
├── Sidebar ซ่อน → เปิดด้วยปุ่ม Hamburger
└── Main content เต็มจอ

Mobile (< 768px)
├── Sidebar เป็น Drawer slide-in จากซ้าย
├── Bottom Navigation Bar แทน Sidebar
└── Kanban board เลื่อนซ้าย-ขวาได้ (horizontal scroll)
```

---

### Component Design

**Sidebar**
- พื้นหลัง `#151820` border ขวา `#252a38`
- Logo + icon gradient `#4f8ef7` → `#7c5cfc`
- Nav item active: `rgba(79,142,247,0.12)` ตัวอักษรสี `#4f8ef7`
- Badge แจ้งเตือนขวาของ nav item

**Cards**
- พื้นหลัง `#151820` border `#252a38` border-radius `12px`
- Hover: border เปลี่ยนเป็น `rgba(79,142,247,0.3)` พร้อม transition
- Shadow: `0 4px 24px rgba(0,0,0,0.3)`

**Buttons**
- Primary: gradient `#4f8ef7` → `#7c5cfc` ข้อความขาว
- Secondary: border `#252a38` พื้นหลัง transparent
- Danger: พื้นหลัง `rgba(244,63,94,0.15)` ตัวอักษร `#f43f5e`
- border-radius ทุกปุ่ม: `8px`

**Inputs & Forms**
- พื้นหลัง `#1c2030` border `#252a38` border-radius `8px`
- Focus: border `#4f8ef7` + glow `0 0 0 3px rgba(79,142,247,0.15)`
- Placeholder สี `#6b7280`

**Priority Badges**
```
Urgent  → #f43f5e (แดง)
High    → #f97316 (ส้ม)
Medium  → #fbbf24 (เหลือง)
Low     → #22d3a0 (เขียว)
```

**Status Badges (Task)**
```
Todo        → border #252a38, text #6b7280
In Progress → bg rgba(249,115,22,0.15), text #f97316
Review      → bg rgba(124,92,252,0.15), text #7c5cfc
Done        → bg rgba(34,211,160,0.15), text #22d3a0
```

**Progress Bar**
- Track: `#252a38`
- Fill: gradient `#4f8ef7` → `#22d3a0`
- border-radius: `999px`

---

### Page-by-Page UX

**Dashboard**
- Stats row บนสุด: 4 card แสดง Total Projects, Tasks Today, Hours This Week, Pending Review
- ด้านล่างแบ่ง 2 คอลัมน์: My Tasks (ซ้าย) + Activity Feed (ขวา)
- Activity Feed แสดง avatar + ชื่อ + action + เวลา เช่น "สมชาย อัปเดต task 'Design UI' → Done • 5 นาทีที่แล้ว"

**Kanban Board**
- 4 คอลัมน์แนวนอน scroll ได้บน mobile
- แต่ละ card แสดง: ชื่องาน, priority badge, avatar ผู้รับผิดชอบ, due date
- Drag & drop ด้วย SortableJS
- คอลัมน์มีตัวเลขนับ task อยู่ด้านบน

**Work Log Form**
- Form กลางหน้า: เลือก Task (dropdown) + วันที่ + ชั่วโมง + รายละเอียด
- Timer widget: วงกลม progress + ตัวเลข HH:MM:SS + ปุ่ม Start/Stop สีเขียว/แดง
- ประวัติ work log วันนี้แสดงด้านล่าง

**Notification**
- Bell icon มุมขวาบน Topbar พร้อม badge นับจำนวน
- Dropdown แสดงรายการแจ้งเตือน อ่านแล้ว/ยังไม่อ่าน แตกต่างกัน

---

### Micro-interactions & Animation

```css
/* Transition มาตรฐาน */
transition: all 0.15s ease;

/* Card hover */
transform: translateY(-2px);
box-shadow: 0 8px 32px rgba(0,0,0,0.4);

/* Button active */
transform: scale(0.97);

/* Page fade-in */
animation: fadeIn 0.3s ease;

/* Skeleton loading */
background: linear-gradient(90deg, #151820 25%, #1c2030 50%, #151820 75%);
background-size: 200% 100%;
animation: shimmer 1.5s infinite;
```

---

### Accessibility

- Contrast ratio ข้อความ/พื้นหลัง ≥ 4.5:1 ทุกจุด
- Focus ring ทุก interactive element
- `aria-label` บน icon buttons
- Keyboard navigation รองรับ

---

## Database Schema

```sql
departments
- id, name, description, timestamps

users
- id, name, email, password, department_id, role, avatar, timestamps

projects
- id, name, description, status (planning/in_progress/done), deadline, created_by, timestamps

project_user (pivot)
- project_id, user_id, timestamps

tasks
- id, project_id, title, description, status (todo/in_progress/review/done), priority (low/medium/high/urgent), assigned_to (user_id), due_date, timestamps

work_logs
- id, user_id, task_id, date, hours, description, timestamps

task_updates (ประวัติการเปลี่ยนแปลง)
- id, task_id, user_id, old_status, new_status, note, timestamps

comments
- id, task_id, user_id, body, timestamps

attachments
- id, task_id, user_id, file_name, file_path, file_size, timestamps

notifications (in-app)
- id, user_id, type, data (json), read_at, timestamps
```

---

## Roles & Permissions

### admin
- จัดการทุกอย่างในระบบ
- จัดการ user, department, project, task
- ดู report และ audit log ทั้งหมด

### manager
- ดู/จัดการ project และ task ในทีมของตัวเอง
- assign งานให้พนักงาน
- ดู work log และ report ของทีม
- export รายงาน

### employee
- ดูเฉพาะ project และ task ที่ตัวเองรับผิดชอบ
- อัปเดต status งานของตัวเอง
- กรอก work log รายวัน
- comment และ attach ไฟล์ใน task ของตัวเอง

---

## Core Features

### 1. Dashboard
- ภาพรวม project ทั้งหมด พร้อม progress bar (คำนวณจาก task done / total task)
- My Tasks วันนี้
- Activity Feed — อัปเดตด้วย `wire:poll.15s`
- Work summary ชั่วโมงรวมรายสัปดาห์

### 2. Project Management
- CRUD project
- กำหนด deadline, status, assign พนักงานเข้า project
- ดูภาพรวม progress ของแต่ละ project

### 3. Task Management
- Kanban board (Todo → In Progress → Review → Done)
- กำหนด priority, due date, assigned user
- drag & drop เปลี่ยน status (ใช้ SortableJS)
- Kanban อัปเดตด้วย `wire:poll.15s`
- บันทึกประวัติการเปลี่ยนแปลง status ทุกครั้ง

### 4. Work Log
- พนักงานกรอกรายวัน: วันที่ / task / ชั่วโมง / รายละเอียดที่ทำ
- มีปุ่ม Start/Stop Timer จับเวลาแบบ real-time แล้วบันทึกอัตโนมัติ
- ผู้จัดการดู work log รายบุคคลหรือ summary รายสัปดาห์/เดือนได้

### 5. Polling (แทน Real-time WebSocket)
- ใช้ Livewire `wire:poll.15s` สำหรับ:
  - Activity Feed
  - Kanban board
  - Notification badge
- Request เข้า server ประมาณ 40 ครั้ง/นาที (10 คน) — Shared Hosting รับได้สบาย

### 6. Notification
- In-app notification เมื่อมีการ assign งาน, deadline ใกล้ถึง, มี comment ใหม่
- รองรับ Email notification ผ่าน Laravel Notification
- เตรียม structure ให้รองรับ LINE Notify ในอนาคต

### 7. Calendar View
- แสดง deadline ของ task และ project บน FullCalendar.js
- ดึงข้อมูลผ่าน Laravel API endpoint

### 8. File Attachment
- แนบไฟล์ใน task ได้
- เก็บใน Laravel Storage local disk
- แสดงรายการไฟล์และดาวน์โหลดได้

### 9. Comment
- comment ใน task ได้
- แสดงผลใหม่ผ่าน `wire:poll.15s`

### 10. Reporting & Export
- รายงาน work log รายเดือนต่อคน
- summary ภาพรวมโครงการ
- export เป็น Excel (Laravel Excel) และ PDF (DomPDF)

### 11. Audit Log
- บันทึกทุก action ในระบบ ใช้ Spatie Activity Log
- admin ดู log ย้อนหลังได้

---

## โครงสร้างโฟลเดอร์

```
app/
├── Models/
│   ├── User, Department, Project, Task
│   ├── WorkLog, TaskUpdate, Comment, Attachment
├── Http/Controllers/
│   ├── DashboardController
│   ├── ProjectController
│   ├── TaskController
│   ├── WorkLogController
│   ├── CommentController
│   ├── ReportController
├── Livewire/
│   ├── KanbanBoard       ← wire:poll.15s
│   ├── WorkLogForm
│   ├── ActivityFeed      ← wire:poll.15s
│   ├── Timer
│   ├── NotificationBell  ← wire:poll.15s
├── Events/
│   ├── TaskUpdated
│   ├── WorkLogCreated
│   ├── CommentCreated
├── Listeners/
├── Notifications/
│   ├── TaskAssigned
│   ├── DeadlineReminder
│   ├── NewComment
```

---

## Polling Flow (แทน WebSocket)

```
พนักงานอัปเดต task status
  → Livewire Component อัปเดต DB
  → บันทึก task_updates history

ฝั่ง browser ของคนอื่นในทีม
  → wire:poll.15s ส่ง request ทุก 15 วินาที
  → Livewire ดึงข้อมูลใหม่จาก DB
  → UI อัปเดตอัตโนมัติ
```

---

## Scope ที่ไม่ต้องทำ

- ไม่ต้องทำ Multi-tenant
- ไม่ต้องแยก subdomain หรือ database ต่อองค์กร
- ไม่ต้องทำ Mobile App (Web เท่านั้น)
- ไม่ต้องติดตั้ง Laravel Reverb / WebSocket
- ไม่ต้องใช้ Redis หรือ Supervisor
