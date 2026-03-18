import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;

/**
 * StudentManagementSystem Class
 * Responsible for managing the entire student system
 */
public class StudentManagementSystem {
    private List<Student> students;
    private Scanner scanner;

    // Constructor
    public StudentManagementSystem() {
        this.students = new ArrayList<>();
        this.scanner = new Scanner(System.in);
    }

    /**
     * Add a new student to the system
     */
    public void addStudent() {
        System.out.println("\n========== Add New Student ==========");

        String id = getValidId("Enter Student ID: ");
        String name = getValidName("Enter Student Name: ");
        String major = getValidMajor("Enter Major: ");

        Student student = new Student(id, name, major);
        students.add(student);

        System.out.println("Student added successfully!");
        System.out.println("======================================");
    }

    /**
     * Get valid student ID (non-empty)
     */
    private String getValidId(String prompt) {
        String id;
        while (true) {
            System.out.print(prompt);
            id = scanner.nextLine().trim();
            if (!id.isEmpty()) {
                // Check if ID already exists
                boolean exists = false;
                for (Student s : students) {
                    if (s.getId().equals(id)) {
                        System.out.println("Error: This ID already exists. Please enter a different ID.");
                        exists = true;
                        break;
                    }
                }
                if (!exists) {
                    return id;
                }
            } else {
                System.out.println("Error: ID cannot be empty.");
            }
        }
    }

    /**
     * Get valid student name (non-empty)
     */
    private String getValidName(String prompt) {
        String name;
        while (true) {
            System.out.print(prompt);
            name = scanner.nextLine().trim();
            if (!name.isEmpty()) {
                return name;
            } else {
                System.out.println("Error: Name cannot be empty.");
            }
        }
    }

    /**
     * Get valid major (non-empty)
     */
    private String getValidMajor(String prompt) {
        String major;
        while (true) {
            System.out.print(prompt);
            major = scanner.nextLine().trim();
            if (!major.isEmpty()) {
                return major;
            } else {
                System.out.println("Error: Major cannot be empty.");
            }
        }
    }

    /**
     * Find a student by ID
     * 
     * @param id The student ID to search for
     * @return The student if found, null otherwise
     */
    public Student findStudent(String id) {
        for (Student student : students) {
            if (student.getId().equals(id)) {
                return student;
            }
        }
        return null;
    }

    /**
     * Add a subject to a specific student
     */
    public void addSubjectToStudent() {
        System.out.println("\n========== Add Subject to Student ==========");

        String studentId = getValidInput("Enter Student ID: ");
        Student student = findStudent(studentId);

        if (student == null) {
            System.out.println("Error: Student not found!");
            System.out.println("============================================");
            return;
        }

        String subjectName = getValidInput("Enter Subject Name: ");
        int creditHours = getValidCreditHours();
        double grade = getValidGrade();

        Subject subject = new Subject(subjectName, creditHours, grade);
        student.addSubject(subject);

        System.out.println("Subject added successfully to " + student.getName() + "!");
        System.out.println("============================================");
    }

    /**
     * Get valid non-empty input
     */
    private String getValidInput(String prompt) {
        String input;
        while (true) {
            System.out.print(prompt);
            input = scanner.nextLine().trim();
            if (!input.isEmpty()) {
                return input;
            } else {
                System.out.println("Error: This field cannot be empty.");
            }
        }
    }

    /**
     * Get valid credit hours (positive integer)
     */
    private int getValidCreditHours() {
        int creditHours;
        while (true) {
            System.out.print("Enter Credit Hours: ");
            try {
                creditHours = Integer.parseInt(scanner.nextLine().trim());
                if (creditHours > 0) {
                    return creditHours;
                } else {
                    System.out.println("Error: Credit hours must be greater than 0.");
                }
            } catch (NumberFormatException e) {
                System.out.println("Error: Please enter a valid number.");
            }
        }
    }

    /**
     * Get valid grade (0-100)
     */
    private double getValidGrade() {
        double grade;
        while (true) {
            System.out.print("Enter Grade (0-100): ");
            try {
                grade = Double.parseDouble(scanner.nextLine().trim());
                if (grade >= 0 && grade <= 100) {
                    return grade;
                } else {
                    System.out.println("Error: Grade must be between 0 and 100.");
                }
            } catch (NumberFormatException e) {
                System.out.println("Error: Please enter a valid number.");
            }
        }
    }

    /**
     * Display a specific student's information
     */
    public void displayStudentInfo() {
        System.out.println("\n========== Display Student Info ==========");

        String studentId = getValidInput("Enter Student ID: ");
        Student student = findStudent(studentId);

        if (student != null) {
            student.displayStudentInfo();
        } else {
            System.out.println("Error: Student not found!");
        }
        System.out.println("==========================================");
    }

    /**
     * Calculate and display GPA for a specific student
     */
    public void calculateGPA() {
        System.out.println("\n========== Calculate GPA ==========");

        String studentId = getValidInput("Enter Student ID: ");
        Student student = findStudent(studentId);

        if (student != null) {
            double gpa = student.calculateGPA();
            System.out.println("Student: " + student.getName());
            System.out.println("Major: " + student.getMajor());
            System.out.printf("GPA: %.2f%n", gpa);
        } else {
            System.out.println("Error: Student not found!");
        }
        System.out.println("=================================");
    }

    /**
     * Display all students in the system
     */
    public void displayAllStudents() {
        System.out.println("\n========== All Students ==========");

        if (students.isEmpty()) {
            System.out.println("No students in the system.");
        } else {
            System.out.println("Total Students: " + students.size());
            System.out.println("-----------------------------------");
            for (Student student : students) {
                System.out.println("ID: " + student.getId() +
                        " | Name: " + student.getName() +
                        " | Major: " + student.getMajor() +
                        " | Subjects: " + student.getSubjects().size() +
                        " | GPA: " + String.format("%.2f", student.calculateGPA()));
            }
        }
        System.out.println("===================================");
    }

    /**
     * Run the main menu
     */
    public void run() {
        int choice;
        do {
            displayMenu();
            choice = getMenuChoice();

            switch (choice) {
                case 1:
                    addStudent();
                    break;
                case 2:
                    addSubjectToStudent();
                    break;
                case 3:
                    displayStudentInfo();
                    break;
                case 4:
                    calculateGPA();
                    break;
                case 5:
                    displayAllStudents();
                    break;
                case 6:
                    System.out.println("\nThank you for using Student Management System!");
                    System.out.println("Exiting... Goodbye!");
                    break;
                default:
                    System.out.println("Invalid choice. Please try again.");
            }
        } while (choice != 6);
    }

    /**
     * Display the main menu
     */
    private void displayMenu() {
        System.out.println("\n========== Student Management System ==========");
        System.out.println("1. Add Student");
        System.out.println("2. Add Subject to Student");
        System.out.println("3. Display Student Info");
        System.out.println("4. Calculate GPA");
        System.out.println("5. Display All Students");
        System.out.println("6. Exit");
        System.out.println("=================================================");
    }

    /**
     * Get valid menu choice
     */
    private int getMenuChoice() {
        int choice;
        while (true) {
            System.out.print("Enter your choice (1-6): ");
            try {
                choice = Integer.parseInt(scanner.nextLine().trim());
                if (choice >= 1 && choice <= 6) {
                    return choice;
                } else {
                    System.out.println("Error: Please enter a number between 1 and 6.");
                }
            } catch (NumberFormatException e) {
                System.out.println("Error: Please enter a valid number.");
            }
        }
    }
}
