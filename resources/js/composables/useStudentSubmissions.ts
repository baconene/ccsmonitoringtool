import { ref } from 'vue';
import axios from 'axios';

export interface StudentSubmission {
    id: number;
    student_id: number;
    student_name: string;
    progress: number;
    score: number | null;
    total_score?: number;
    status: 'not_started' | 'in_progress' | 'submitted' | 'graded' | 'late';
    submitted_at?: string;
    graded_at?: string;
}

export function useStudentSubmissions() {
    const submissions = ref<StudentSubmission[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    /**
     * Fetch submissions for an assignment
     */
    const fetchAssignmentSubmissions = async (assignmentId: number) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get(`/instructor/assignments/${assignmentId}/submissions`);
            submissions.value = formatAssignmentSubmissions(response.data);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch submissions';
            console.error('Error fetching assignment submissions:', err);
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch submissions for a quiz
     */
    const fetchQuizSubmissions = async (quizId: number) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get(`/instructor/activities/${quizId}/quiz-results`);
            submissions.value = formatQuizSubmissions(response.data);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch quiz results';
            console.error('Error fetching quiz submissions:', err);
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch submissions for a project
     */
    const fetchProjectSubmissions = async (projectId: number) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get(`/instructor/projects/${projectId}/submissions`);
            submissions.value = formatProjectSubmissions(response.data);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch project submissions';
            console.error('Error fetching project submissions:', err);
        } finally {
            loading.value = false;
        }
    };

    /**
     * Format assignment submissions
     */
    const formatAssignmentSubmissions = (data: any[]): StudentSubmission[] => {
        return data.map((item) => ({
            id: item.id,
            student_id: item.student_id,
            student_name: item.student_name || item.student?.name || 'Unknown Student',
            progress: item.progress ?? (item.status === 'submitted' || item.status === 'graded' ? 100 : 
                     item.status === 'in_progress' ? 50 : 0),
            score: item.score,
            total_score: item.total_score,
            status: mapSubmissionStatus(item.status, item.submitted_at, item.due_date),
            submitted_at: item.submitted_at,
            graded_at: item.graded_at,
        }));
    };

    /**
     * Format quiz submissions
     */
    const formatQuizSubmissions = (data: any[]): StudentSubmission[] => {
        return data.map((item) => ({
            id: item.id,
            student_id: item.student_id,
            student_name: item.student?.name || 'Unknown Student',
            progress: item.completed_at ? 100 : 
                     item.started_at ? 50 : 0,
            score: item.score,
            total_score: item.total_points,
            status: item.completed_at ? 'graded' : 
                   item.started_at ? 'in_progress' : 'not_started',
            submitted_at: item.completed_at,
            graded_at: item.completed_at,
        }));
    };

    /**
     * Format project submissions
     */
    const formatProjectSubmissions = (data: any[]): StudentSubmission[] => {
        return data.map((item) => ({
            id: item.id,
            student_id: item.student_id,
            student_name: item.student?.name || 'Unknown Student',
            progress: item.status === 'submitted' || item.status === 'graded' ? 100 : 
                     item.status === 'in_progress' ? 50 : 0,
            score: item.score,
            total_score: item.total_score,
            status: mapSubmissionStatus(item.status, item.submitted_at, item.due_date),
            submitted_at: item.submitted_at,
            graded_at: item.graded_at,
        }));
    };

    /**
     * Map submission status and check if late
     */
    const mapSubmissionStatus = (
        status: string,
        submittedAt?: string,
        dueDate?: string
    ): StudentSubmission['status'] => {
        if (!status) return 'not_started';
        
        // Check if submission is late
        if (submittedAt && dueDate) {
            const submitted = new Date(submittedAt);
            const due = new Date(dueDate);
            if (submitted > due && status === 'submitted') {
                return 'late';
            }
        }

        const statusMap: Record<string, StudentSubmission['status']> = {
            'not_started': 'not_started',
            'in_progress': 'in_progress',
            'submitted': 'submitted',
            'graded': 'graded',
            'pending': 'submitted',
            'completed': 'graded',
        };

        return statusMap[status] || 'not_started';
    };

    return {
        submissions,
        loading,
        error,
        fetchAssignmentSubmissions,
        fetchQuizSubmissions,
        fetchProjectSubmissions,
    };
}
