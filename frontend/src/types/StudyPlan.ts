export type StudyPlanStatus = 'pending' | 'in_progress' | 'done' | 'overdue';

export type StudyPlan = {
  id: string;
  userId: number;
  title: string;
  deadline: string;
  status: StudyPlanStatus;
  createdAt: string;
  updatedAt: string;
};

export type DashboardStats = Record<StudyPlanStatus, number>;

export type WeeklyGoals = Record<StudyPlanStatus, number>;

export type WeeklyProgress = {
  week: string;
  goals: WeeklyGoals;
  counts: WeeklyGoals;
  percentages: WeeklyGoals;
};
