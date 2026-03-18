/**
 * Subject Class
 * Represents a course/subject with its name, credit hours, and grade
 */
public class Subject {
    private String subjectName;
    private int creditHours;
    private double grade;

    // Constructor
    public Subject(String subjectName, int creditHours, double grade) {
        this.subjectName = subjectName;
        this.creditHours = creditHours;
        this.grade = grade;
    }

    // Getters
    public String getSubjectName() {
        return subjectName;
    }

    public int getCreditHours() {
        return creditHours;
    }

    public double getGrade() {
        return grade;
    }

    // Setters
    public void setSubjectName(String subjectName) {
        this.subjectName = subjectName;
    }

    public void setCreditHours(int creditHours) {
        this.creditHours = creditHours;
    }

    public void setGrade(double grade) {
        this.grade = grade;
    }

    @Override
    public String toString() {
        return "Subject: " + subjectName + 
               " | Credit Hours: " + creditHours + 
               " | Grade: " + grade;
    }
}
