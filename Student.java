import java.util.ArrayList;
import java.util.List;

/**
 * Student Class
 * Represents a student with their personal information and subjects
 */
public class Student {
    private String id;
    private String name;
    private String major;
    private List<Subject> subjects;

    // Constructor
    public Student(String id, String name, String major) {
        this.id = id;
        this.name = name;
        this.major = major;
        this.subjects = new ArrayList<>();
    }

    // Getters
    public String getId() {
        return id;
    }

    public String getName() {
        return name;
    }

    public String getMajor() {
        return major;
    }

    public List<Subject> getSubjects() {
        return subjects;
    }

    // Setters
    public void setId(String id) {
        this.id = id;
    }

    public void setName(String name) {
        this.name = name;
    }

    public void setMajor(String major) {
        this.major = major;
    }

    /**
     * Add a subject to the student's record
     * 
     * @param subject The subject to add
     * @return true if added successfully, false otherwise
     */
    public boolean addSubject(Subject subject) {
        if (subject != null) {
            subjects.add(subject);
            return true;
        }
        return false;
    }

    /**
     * Calculate GPA using the formula:
     * GPA = Σ(grade × creditHours) / Σ(creditHours)
     * 
     * @return The calculated GPA, or 0 if no subjects
     */
    public double calculateGPA() {
        if (subjects.isEmpty()) {
            return 0.0;
        }

        double totalGradePoints = 0.0;
        int totalCreditHours = 0;

        for (Subject subject : subjects) {
            totalGradePoints += subject.getGrade() * subject.getCreditHours();
            totalCreditHours += subject.getCreditHours();
        }

        if (totalCreditHours == 0) {
            return 0.0;
        }

        return totalGradePoints / totalCreditHours;
    }

    /**
     * Display all student information including subjects and GPA
     */
    public void displayStudentInfo() {
        System.out.println("\n========== Student Information ==========");
        System.out.println("ID: " + id);
        System.out.println("Name: " + name);
        System.out.println("Major: " + major);
        System.out.println("------------------------------------------");

        if (subjects.isEmpty()) {
            System.out.println("No subjects enrolled.");
        } else {
            System.out.println("Enrolled Subjects:");
            System.out.println("------------------------------------------");
            for (Subject subject : subjects) {
                System.out.println("  - " + subject.toString());
            }
            System.out.println("------------------------------------------");
            System.out.printf("GPA: %.2f%n", calculateGPA());
        }
        System.out.println("==========================================");
    }
}
