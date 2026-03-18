/**
 * Main Class
 * Entry point for the Student Management System
 */
public class Main {
    public static void main(String[] args) {
        System.out.println("==============================================");
        System.out.println("   Welcome to University Student Management  ");
        System.out.println("                   System v1.0                ");
        System.out.println("==============================================");

        StudentManagementSystem sms = new StudentManagementSystem();
        sms.run();
    }
}
