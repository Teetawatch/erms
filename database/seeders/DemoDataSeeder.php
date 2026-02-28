<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use App\Models\WorkLog;
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

        $project1 = Project::create([
            'name' => 'ระบบจัดการลูกค้า CRM',
            'description' => 'พัฒนาระบบ CRM สำหรับจัดการข้อมูลลูกค้าและการติดตามการขาย',
            'status' => 'in_progress',
            'deadline' => now()->addDays(30),
            'created_by' => $admin->id,
        ]);
        $project1->members()->attach([$admin->id, $manager->id, $emp1->id, $emp2->id]);

        $project2 = Project::create([
            'name' => 'แอปพลิเคชันมือถือ',
            'description' => 'พัฒนาแอปมือถือสำหรับลูกค้า',
            'status' => 'planning',
            'deadline' => now()->addDays(60),
            'created_by' => $manager->id,
        ]);
        $project2->members()->attach([$manager->id, $emp1->id, $emp3->id]);

        $tasks = [
            ['project_id' => $project1->id, 'title' => 'ออกแบบ Database Schema', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $emp1->id, 'due_date' => now()->subDays(5)],
            ['project_id' => $project1->id, 'title' => 'ออกแบบ UI หน้า Dashboard', 'status' => 'done', 'priority' => 'high', 'assigned_to' => $emp2->id, 'due_date' => now()->subDays(3)],
            ['project_id' => $project1->id, 'title' => 'พัฒนา API Authentication', 'status' => 'in_progress', 'priority' => 'urgent', 'assigned_to' => $emp1->id, 'due_date' => now()->addDays(2)],
            ['project_id' => $project1->id, 'title' => 'พัฒนาหน้า Customer List', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $emp1->id, 'due_date' => now()->addDays(7)],
            ['project_id' => $project1->id, 'title' => 'ทดสอบระบบ Login', 'status' => 'review', 'priority' => 'high', 'assigned_to' => $emp3->id, 'due_date' => now()->addDays(1)],
            ['project_id' => $project1->id, 'title' => 'ออกแบบ Logo', 'status' => 'in_progress', 'priority' => 'low', 'assigned_to' => $emp2->id, 'due_date' => now()->addDays(10)],
            ['project_id' => $project2->id, 'title' => 'วิเคราะห์ Requirements', 'status' => 'todo', 'priority' => 'high', 'assigned_to' => $manager->id, 'due_date' => now()->addDays(14)],
            ['project_id' => $project2->id, 'title' => 'ออกแบบ Wireframe', 'status' => 'todo', 'priority' => 'medium', 'assigned_to' => $emp2->id, 'due_date' => now()->addDays(21)],
        ];

        foreach ($tasks as $i => $taskData) {
            $taskData['sort_order'] = $i;
            Task::create($taskData);
        }

        $task1 = Task::first();
        TaskUpdate::create(['task_id' => $task1->id, 'user_id' => $emp1->id, 'old_status' => 'todo', 'new_status' => 'in_progress', 'note' => 'เริ่มทำงาน']);
        TaskUpdate::create(['task_id' => $task1->id, 'user_id' => $emp1->id, 'old_status' => 'in_progress', 'new_status' => 'done', 'note' => 'เสร็จแล้ว']);

        WorkLog::create(['user_id' => $emp1->id, 'task_id' => $task1->id, 'date' => now()->subDays(1), 'hours' => 4.5, 'description' => 'ออกแบบ schema สำหรับ customers, orders']);
        WorkLog::create(['user_id' => $emp1->id, 'task_id' => $task1->id, 'date' => now(), 'hours' => 2.0, 'description' => 'Review และปรับปรุง schema']);
        WorkLog::create(['user_id' => $emp2->id, 'task_id' => Task::find(2)->id, 'date' => now(), 'hours' => 6.0, 'description' => 'ออกแบบ Dashboard mockup']);

        Comment::create(['task_id' => $task1->id, 'user_id' => $manager->id, 'body' => 'Schema ดูดีแล้ว ขอเพิ่ม index สำหรับ search ด้วย']);
        Comment::create(['task_id' => $task1->id, 'user_id' => $emp1->id, 'body' => 'เพิ่ม index เรียบร้อยแล้วครับ']);
    }
}
