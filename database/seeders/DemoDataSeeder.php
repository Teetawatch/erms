<?php

namespace Database\Seeders;

use App\Models\AutomationRule;
use App\Models\Comment;
use App\Models\CustomField;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\TaskTemplate;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $dev = Department::create(['name' => 'พัฒนาระบบ', 'description' => 'ทีมพัฒนาซอฟต์แวร์']);
        $design = Department::create(['name' => 'ออกแบบ', 'description' => 'ทีมออกแบบ UI/UX']);
        $qa = Department::create(['name' => 'ทดสอบระบบ', 'description' => 'ทีม QA']);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@erms.test',
            'password' => Hash::make('password'),
            'department_id' => $dev->id,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $manager = User::create([
            'name' => 'สมชาย จัดการ',
            'email' => 'manager@erms.test',
            'password' => Hash::make('password'),
            'department_id' => $dev->id,
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('manager');

        $emp1 = User::create([
            'name' => 'สมหญิง พัฒนา',
            'email' => 'employee1@erms.test',
            'password' => Hash::make('password'),
            'department_id' => $dev->id,
            'email_verified_at' => now(),
        ]);
        $emp1->assignRole('employee');

        $emp2 = User::create([
            'name' => 'วิชัย ดีไซน์',
            'email' => 'employee2@erms.test',
            'password' => Hash::make('password'),
            'department_id' => $design->id,
            'email_verified_at' => now(),
        ]);
        $emp2->assignRole('employee');

        $emp3 = User::create([
            'name' => 'มานี ทดสอบ',
            'email' => 'employee3@erms.test',
            'password' => Hash::make('password'),
            'department_id' => $qa->id,
            'email_verified_at' => now(),
        ]);
        $emp3->assignRole('employee');

        // --- Projects ---
        $project1 = Project::create([
            'name' => 'ระบบจัดการลูกค้า CRM',
            'description' => 'พัฒนาระบบ CRM สำหรับจัดการข้อมูลลูกค้าและการติดตามการขาย',
            'status' => 'in_progress',
            'start_date' => now()->subDays(10),
            'deadline' => now()->addDays(30),
            'created_by' => $admin->id,
        ]);
        $project1->members()->attach([$admin->id, $manager->id, $emp1->id, $emp2->id]);

        $project2 = Project::create([
            'name' => 'แอปพลิเคชันมือถือ',
            'description' => 'พัฒนาแอปมือถือสำหรับลูกค้า',
            'status' => 'planning',
            'start_date' => now()->addDays(5),
            'deadline' => now()->addDays(60),
            'created_by' => $manager->id,
        ]);
        $project2->members()->attach([$manager->id, $emp1->id, $emp3->id]);

        $project3 = Project::create([
            'name' => 'ปรับปรุงเว็บไซต์',
            'description' => 'ปรับปรุง UI/UX ของเว็บไซต์บริษัท',
            'status' => 'in_progress',
            'start_date' => now()->subDays(5),
            'deadline' => now()->addDays(20),
            'created_by' => $admin->id,
        ]);
        $project3->members()->attach([$admin->id, $emp2->id, $emp3->id]);

        // --- Tasks with start_date for Timeline ---
        $tasks = [
            ['project_id' => $project1->id, 'title' => 'ออกแบบ Database Schema', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $emp1->id, 'start_date' => now()->subDays(10), 'due_date' => now()->subDays(5), 'estimated_hours' => 16, 'progress' => 100],
            ['project_id' => $project1->id, 'title' => 'ออกแบบ UI หน้า Dashboard', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $emp2->id, 'start_date' => now()->subDays(8), 'due_date' => now()->subDays(3), 'estimated_hours' => 12, 'progress' => 100],
            ['project_id' => $project1->id, 'title' => 'พัฒนา API Authentication', 'status' => 'in_progress', 'priority' => 'urgent', 'assigned_to' => $emp1->id, 'start_date' => now()->subDays(3), 'due_date' => now()->addDays(2), 'estimated_hours' => 24, 'progress' => 60, 'tags' => ['backend', 'security']],
            ['project_id' => $project1->id, 'title' => 'พัฒนาหน้า Customer List', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $emp1->id, 'start_date' => now()->addDays(3), 'due_date' => now()->addDays(7), 'estimated_hours' => 16, 'progress' => 0, 'tags' => ['frontend']],
            ['project_id' => $project1->id, 'title' => 'ทดสอบระบบ Login', 'status' => 'review', 'priority' => 'high', 'assigned_to' => $emp3->id, 'start_date' => now()->subDays(1), 'due_date' => now()->addDays(1), 'estimated_hours' => 8, 'progress' => 80, 'tags' => ['testing']],
            ['project_id' => $project1->id, 'title' => 'ออกแบบ Logo', 'status' => 'in_progress', 'priority' => 'low', 'assigned_to' => $emp2->id, 'start_date' => now(), 'due_date' => now()->addDays(10), 'estimated_hours' => 6, 'progress' => 30],
            ['project_id' => $project1->id, 'title' => 'สร้างระบบ Report', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $emp1->id, 'start_date' => now()->addDays(8), 'due_date' => now()->addDays(15), 'estimated_hours' => 20, 'progress' => 0, 'tags' => ['backend', 'report']],
            ['project_id' => $project2->id, 'title' => 'วิเคราะห์ Requirements', 'status' => 'todo', 'priority' => 'high', 'assigned_to' => $manager->id, 'start_date' => now()->addDays(5), 'due_date' => now()->addDays(14), 'estimated_hours' => 16, 'progress' => 0],
            ['project_id' => $project2->id, 'title' => 'ออกแบบ Wireframe', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $emp2->id, 'start_date' => now()->addDays(15), 'due_date' => now()->addDays(21), 'estimated_hours' => 20, 'progress' => 0],
            ['project_id' => $project2->id, 'title' => 'พัฒนา Prototype', 'status' => 'todo', 'priority' => 'high', 'assigned_to' => $emp1->id, 'start_date' => now()->addDays(22), 'due_date' => now()->addDays(35), 'estimated_hours' => 40, 'progress' => 0],
            ['project_id' => $project3->id, 'title' => 'ตรวจสอบ Accessibility', 'status' => 'in_progress', 'priority' => 'medium', 'assigned_to' => $emp3->id, 'start_date' => now()->subDays(3), 'due_date' => now()->addDays(5), 'estimated_hours' => 10, 'progress' => 40],
            ['project_id' => $project3->id, 'title' => 'ปรับปรุง Homepage', 'status' => 'todo', 'priority' => 'high', 'assigned_to' => $emp2->id, 'start_date' => now()->addDays(1), 'due_date' => now()->addDays(8), 'estimated_hours' => 16, 'progress' => 0],
        ];

        $createdTasks = [];
        foreach ($tasks as $i => $taskData) {
            $taskData['sort_order'] = $i;
            $createdTasks[] = Task::create($taskData);
        }

        // --- Subtasks ---
        $apiTask = $createdTasks[2]; // พัฒนา API Authentication
        Task::create(['project_id' => $project1->id, 'parent_id' => $apiTask->id, 'title' => 'สร้าง JWT Token System', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $emp1->id, 'sort_order' => 0]);
        Task::create(['project_id' => $project1->id, 'parent_id' => $apiTask->id, 'title' => 'เชื่อมต่อ OAuth Provider', 'status' => 'in_progress', 'priority' => 'medium', 'assigned_to' => $emp1->id, 'sort_order' => 1]);
        Task::create(['project_id' => $project1->id, 'parent_id' => $apiTask->id, 'title' => 'สร้าง Middleware Guard', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $emp1->id, 'sort_order' => 2]);

        $reportTask = $createdTasks[6]; // สร้างระบบ Report
        Task::create(['project_id' => $project1->id, 'parent_id' => $reportTask->id, 'title' => 'ออกแบบ Report Template', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $emp2->id, 'sort_order' => 0]);
        Task::create(['project_id' => $project1->id, 'parent_id' => $reportTask->id, 'title' => 'พัฒนา PDF Export', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $emp1->id, 'sort_order' => 1]);

        // --- Task Dependencies ---
        // Customer List depends on Database Schema being done
        TaskDependency::create(['task_id' => $createdTasks[3]->id, 'depends_on_task_id' => $createdTasks[0]->id, 'type' => 'finish_to_start']);
        // Testing Login depends on API Auth
        TaskDependency::create(['task_id' => $createdTasks[4]->id, 'depends_on_task_id' => $createdTasks[2]->id, 'type' => 'finish_to_start']);
        // Report depends on Customer List
        TaskDependency::create(['task_id' => $createdTasks[6]->id, 'depends_on_task_id' => $createdTasks[3]->id, 'type' => 'finish_to_start']);
        // Wireframe depends on Requirements
        TaskDependency::create(['task_id' => $createdTasks[8]->id, 'depends_on_task_id' => $createdTasks[7]->id, 'type' => 'finish_to_start']);
        // Prototype depends on Wireframe
        TaskDependency::create(['task_id' => $createdTasks[9]->id, 'depends_on_task_id' => $createdTasks[8]->id, 'type' => 'finish_to_start']);

        // --- Task Updates ---
        TaskUpdate::create(['task_id' => $createdTasks[0]->id, 'user_id' => $emp1->id, 'old_status' => 'todo', 'new_status' => 'in_progress', 'note' => 'เริ่มทำงาน']);
        TaskUpdate::create(['task_id' => $createdTasks[0]->id, 'user_id' => $emp1->id, 'old_status' => 'in_progress', 'new_status' => 'done', 'note' => 'เสร็จแล้ว']);
        TaskUpdate::create(['task_id' => $createdTasks[2]->id, 'user_id' => $emp1->id, 'old_status' => 'todo', 'new_status' => 'in_progress', 'note' => 'เริ่มพัฒนา API']);
        TaskUpdate::create(['task_id' => $createdTasks[4]->id, 'user_id' => $emp3->id, 'old_status' => 'todo', 'new_status' => 'review', 'note' => 'ส่งตรวจสอบ']);

        // --- Comments ---
        Comment::create(['task_id' => $createdTasks[0]->id, 'user_id' => $manager->id, 'body' => 'Schema ดูดีแล้ว ขอเพิ่ม index สำหรับ search ด้วย']);
        Comment::create(['task_id' => $createdTasks[0]->id, 'user_id' => $emp1->id, 'body' => 'เพิ่ม index เรียบร้อยแล้วครับ']);
        Comment::create(['task_id' => $createdTasks[2]->id, 'user_id' => $manager->id, 'body' => 'ใช้ JWT ร่วมกับ OAuth2 ด้วยนะ @สมหญิง']);
        Comment::create(['task_id' => $createdTasks[4]->id, 'user_id' => $emp3->id, 'body' => 'พบ bug ตอน login ด้วย email ที่มี uppercase ครับ']);

        // --- Custom Fields ---
        $cfPriority = CustomField::create(['project_id' => $project1->id, 'name' => 'ระดับความเสี่ยง', 'type' => 'select', 'options' => ['ต่ำ', 'ปานกลาง', 'สูง', 'วิกฤต'], 'sort_order' => 0]);
        CustomField::create(['project_id' => $project1->id, 'name' => 'Story Points', 'type' => 'number', 'sort_order' => 1]);
        CustomField::create(['project_id' => $project1->id, 'name' => 'Sprint', 'type' => 'select', 'options' => ['Sprint 1', 'Sprint 2', 'Sprint 3'], 'sort_order' => 2]);
        CustomField::create(['project_id' => $project2->id, 'name' => 'Platform', 'type' => 'select', 'options' => ['iOS', 'Android', 'Both'], 'sort_order' => 0]);

        // --- Automation Rules ---
        AutomationRule::create([
            'project_id' => $project1->id,
            'name' => 'ย้ายงาน Review เสร็จ ไป Done',
            'trigger_type' => 'status_changed',
            'trigger_conditions' => ['from' => 'review', 'to' => null],
            'action_type' => 'change_status',
            'action_data' => ['value' => 'done'],
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        AutomationRule::create([
            'project_id' => $project1->id,
            'name' => 'แจ้งเตือนเมื่อถึงกำหนด',
            'trigger_type' => 'due_date_reached',
            'trigger_conditions' => [],
            'action_type' => 'send_notification',
            'action_data' => ['value' => 'งานใกล้ถึงกำหนดแล้ว กรุณาตรวจสอบ'],
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        AutomationRule::create([
            'project_id' => $project1->id,
            'name' => 'ตั้งค่า Priority เป็น urgent เมื่อมอบหมาย',
            'trigger_type' => 'task_assigned',
            'trigger_conditions' => [],
            'action_type' => 'set_priority',
            'action_data' => ['value' => 'urgent'],
            'is_active' => false,
            'created_by' => $manager->id,
        ]);

        // --- Task Templates ---
        TaskTemplate::create([
            'name' => 'Bug Fix Workflow',
            'description' => 'ขั้นตอนมาตรฐานสำหรับแก้ไข Bug',
            'task_data' => [
                'title' => 'แก้ไข Bug: [ชื่อ Bug]',
                'description' => 'รายละเอียด Bug และขั้นตอนการ reproduce',
                'priority' => 'high',
                'estimated_hours' => 4,
                'subtasks' => [
                    ['title' => 'วิเคราะห์สาเหตุ (Root Cause)', 'priority' => 'high'],
                    ['title' => 'แก้ไขโค้ด', 'priority' => 'high'],
                    ['title' => 'เขียน Unit Test', 'priority' => 'medium'],
                    ['title' => 'ทดสอบ Regression', 'priority' => 'medium'],
                    ['title' => 'Deploy to Staging', 'priority' => 'low'],
                ],
            ],
            'created_by' => $admin->id,
            'is_global' => true,
        ]);
        TaskTemplate::create([
            'name' => 'Feature Development',
            'description' => 'ขั้นตอนมาตรฐานสำหรับพัฒนา Feature ใหม่',
            'task_data' => [
                'title' => 'พัฒนา Feature: [ชื่อ Feature]',
                'description' => 'รายละเอียดของ Feature ที่ต้องพัฒนา',
                'priority' => 'medium',
                'estimated_hours' => 16,
                'subtasks' => [
                    ['title' => 'วิเคราะห์ Requirements', 'priority' => 'high'],
                    ['title' => 'ออกแบบ Technical Design', 'priority' => 'high'],
                    ['title' => 'พัฒนา Backend', 'priority' => 'high'],
                    ['title' => 'พัฒนา Frontend', 'priority' => 'high'],
                    ['title' => 'เขียน Test', 'priority' => 'medium'],
                    ['title' => 'Code Review', 'priority' => 'medium'],
                    ['title' => 'QA Testing', 'priority' => 'high'],
                ],
            ],
            'created_by' => $admin->id,
            'is_global' => true,
        ]);
        TaskTemplate::create([
            'name' => 'Design Review',
            'description' => 'ขั้นตอนมาตรฐานสำหรับ Review งานออกแบบ',
            'task_data' => [
                'title' => 'Review Design: [ชื่องาน]',
                'priority' => 'medium',
                'estimated_hours' => 4,
                'subtasks' => [
                    ['title' => 'ตรวจสอบ UI ตาม Spec', 'priority' => 'high'],
                    ['title' => 'ตรวจสอบ Responsive', 'priority' => 'medium'],
                    ['title' => 'ตรวจสอบ Accessibility', 'priority' => 'medium'],
                ],
            ],
            'created_by' => $emp2->id,
            'is_global' => false,
        ]);
    }
}
