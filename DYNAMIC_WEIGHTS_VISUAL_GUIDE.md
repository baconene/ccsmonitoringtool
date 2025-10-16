# Dynamic Grade Weight System - Visual Flow

## 📊 Weight Normalization Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                         MODULE GRADE CALCULATION                     │
└─────────────────────────────────────────────────────────────────────┘

Step 1: CHECK WHAT EXISTS IN MODULE
┌──────────────────────────────┐
│  Module Components Check     │
│  ┌────────────────────────┐  │
│  │ Has Lessons?   [✓/✗]  │  │
│  │ Has Activities? [✓/✗]  │  │
│  └────────────────────────┘  │
└──────────────────────────────┘
              ↓
              
Step 2: ADJUST MODULE COMPONENT WEIGHTS
┌──────────────────────────────────────────────────────────┐
│  Configured: Lessons 20% | Activities 80%                │
│                                                            │
│  ┌─────────────────┬──────────────────┬─────────────────┐│
│  │  Both Exist     │  Only Lessons    │ Only Activities  ││
│  │  ✓ Lessons      │  ✓ Lessons       │ ✗ Lessons        ││
│  │  ✓ Activities   │  ✗ Activities    │ ✓ Activities     ││
│  │                 │                  │                  ││
│  │  USE CONFIG:    │  ADJUST:         │ ADJUST:          ││
│  │  L: 20%         │  L: 100% ←       │ L: 0%            ││
│  │  A: 80%         │  A: 0%           │ A: 100% ←        ││
│  └─────────────────┴──────────────────┴─────────────────┘│
└──────────────────────────────────────────────────────────┘
              ↓
              
Step 3: CHECK ACTIVITY TYPES (if activities exist)
┌──────────────────────────────────────────────────────┐
│  Activity Types Present in Module                    │
│  ┌────────────────────────────────────────────────┐  │
│  │ Quiz        [✓/✗]   Configured: 30%           │  │
│  │ Assignment  [✓/✗]   Configured: 15%           │  │
│  │ Assessment  [✓/✗]   Configured: 35%           │  │
│  │ Exercise    [✓/✗]   Configured: 20%           │  │
│  └────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────┘
              ↓
              
Step 4: NORMALIZE ACTIVITY TYPE WEIGHTS
┌─────────────────────────────────────────────────────────────┐
│  Example: Only Quiz + Assignment exist                      │
│                                                               │
│  Configured Weights:                                         │
│  ├─ Quiz: 30%        ✓ Present                              │
│  ├─ Assignment: 15%  ✓ Present                              │
│  ├─ Assessment: 35%  ✗ Missing                              │
│  └─ Exercise: 20%    ✗ Missing                              │
│                                                               │
│  Total Configured (existing only): 30 + 15 = 45%            │
│                                                               │
│  NORMALIZE TO 100%:                                          │
│  ├─ Quiz: (30/45) × 100 = 66.67% ←                          │
│  └─ Assignment: (15/45) × 100 = 33.33% ←                    │
│                                                               │
│  ✅ Total: 100% (always!)                                    │
└─────────────────────────────────────────────────────────────┘
              ↓
              
Step 5: CALCULATE SCORES
┌──────────────────────────────────────────────────────────────┐
│  Lesson Score:                                                │
│  └─ (Completed Lessons / Total Lessons) × 100               │
│     Example: 2/2 = 100%                                       │
│                                                               │
│  Activity Score (Weighted Average):                          │
│  └─ Σ(Type Score × Normalized Weight)                       │
│     Example: (Quiz 80% × 66.67%) + (Assign 85% × 33.33%)    │
│              = 53.34 + 28.33 = 81.67%                        │
│                                                               │
│  Module Score:                                               │
│  └─ (Lesson Score × Lesson Weight) +                        │
│      (Activity Score × Activity Weight)                      │
│     Example: (100% × 20%) + (81.67% × 80%)                  │
│              = 20 + 65.34 = 85.34%                           │
└──────────────────────────────────────────────────────────────┘
```

## 🔄 Before vs After Comparison

### ❌ BEFORE (Bug):
```
Module: 1 Lesson + 2 Quizzes
Configured: Lessons 20%, Activities 80%
Activity Types: Quiz 30%, Assignment 15%, Assessment 35%, Exercise 20%

Calculation:
├─ Lesson: 90% × 20% = 18 points
├─ Quiz Avg: 85%
└─ Activities: 85% × 80% = 68 points  ← WRONG!

Module Score: 86%

Problem: Quiz was only 30% of activity weight, but system ignored
         the missing 70% from other activity types!
```

### ✅ AFTER (Fixed):
```
Module: 1 Lesson + 2 Quizzes  
Configured: Lessons 20%, Activities 80%
Activity Types: Quiz 30%, Assignment 15%, Assessment 35%, Exercise 20%

Calculation:
├─ Lesson: 90% × 20% = 18 points
├─ Detect: Only Quiz exists in activities
├─ Normalize: Quiz = 30/30 × 100 = 100% ✓
├─ Quiz Avg: 85% × 100% = 85% (activity score)
└─ Activities: 85% × 80% = 68 points ✓ CORRECT!

Module Score: 86%

Fixed: Quiz now gets 100% of activity weight since it's the only
       activity type present!
```

## 📱 Simulator Interface

```
┌────────────────────────────────────────────────────────────┐
│  Grade Simulator                                            │
│  Toggle which components exist to see dynamic weights      │
├────────────────────────────────────────────────────────────┤
│                                                              │
│  ☑ Lessons                                    [20%]        │
│  ┌─────────────────────────────────────────┐              │
│  │ Score: [90]                              │              │
│  └─────────────────────────────────────────┘              │
│                                                              │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━              │
│                                                              │
│  Activity Scores                              [80%]        │
│                                                              │
│  ☑ Quiz                                      [66.7%]       │
│  ┌─────────────────────────────────────────┐              │
│  │ Score: [80]                              │              │
│  └─────────────────────────────────────────┘              │
│                                                              │
│  ☑ Assignment                                [33.3%]       │
│  ┌─────────────────────────────────────────┐              │
│  │ Score: [85]                              │              │
│  └─────────────────────────────────────────┘              │
│                                                              │
│  ☐ Assessment                                [0%]          │
│  ┌─────────────────────────────────────────┐              │
│  │ Score: [88]  (disabled, grayed out)     │              │
│  └─────────────────────────────────────────┘              │
│                                                              │
│  ☐ Exercise                                  [0%]          │
│  ┌─────────────────────────────────────────┐              │
│  │ Score: [80]  (disabled, grayed out)     │              │
│  └─────────────────────────────────────────┘              │
│                                                              │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━              │
│                                                              │
│  📊 Result                                                  │
│  ┌──────────────────────────────────────────────────────┐ │
│  │ Lesson Contribution:    18.00%                       │ │
│  │ Activity Score:         81.67%                       │ │
│  │ Activity Contribution:  65.34%                       │ │
│  │                                                       │ │
│  │         Module Score                                  │ │
│  │            83.34%                                     │ │
│  │              B                                        │ │
│  └──────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────┘
```

## 🔍 Weight Normalization Examples

### Example 1: All Activity Types Present
```
Configured:  Quiz 30% | Assignment 15% | Assessment 35% | Exercise 20%
Present:     ✓         ✓                ✓                ✓
Total:       30 + 15 + 35 + 20 = 100%

Normalized:  Quiz 30% | Assignment 15% | Assessment 35% | Exercise 20%
             (30/100×100) (15/100×100)  (35/100×100)    (20/100×100)
             
Result: Weights unchanged (all present)
```

### Example 2: Only Quiz
```
Configured:  Quiz 30% | Assignment 15% | Assessment 35% | Exercise 20%
Present:     ✓         ✗                ✗                ✗
Total:       30 + 0 + 0 + 0 = 30%

Normalized:  Quiz 100% | Assignment 0% | Assessment 0% | Exercise 0%
             (30/30×100)  (not present)  (not present)  (not present)
             
Result: Quiz takes 100% since it's the only type
```

### Example 3: Quiz + Assignment + Exercise
```
Configured:  Quiz 30% | Assignment 15% | Assessment 35% | Exercise 20%
Present:     ✓         ✓                ✗                ✓
Total:       30 + 15 + 0 + 20 = 65%

Normalized:  Quiz 46.15% | Assignment 23.08% | Assessment 0% | Exercise 30.77%
             (30/65×100)   (15/65×100)         (not present)  (20/65×100)
             
Result: Weights redistributed proportionally among present types
```

## 📈 Grade Calculation Formula

### Complete Formula:
```
MODULE_SCORE = (LESSON_SCORE × LESSON_WEIGHT_USED) + 
               (ACTIVITY_SCORE × ACTIVITY_WEIGHT_USED)

Where:
  LESSON_SCORE = (Completed Lessons / Total Lessons) × 100
  
  ACTIVITY_SCORE = Σ(Type_Score[i] × Normalized_Weight[i])
                   for all present activity types
  
  LESSON_WEIGHT_USED = {
    configured_weight     if has_lessons AND has_activities
    100                   if has_lessons AND NOT has_activities
    0                     if NOT has_lessons
  }
  
  ACTIVITY_WEIGHT_USED = {
    configured_weight     if has_lessons AND has_activities
    100                   if NOT has_lessons AND has_activities
    0                     if NOT has_activities
  }
  
  Normalized_Weight[type] = (Configured_Weight[type] / Total_Configured) × 100
                            where Total_Configured = Σ Configured_Weight
                            for all present types
```

### Constraints:
```
✓ LESSON_WEIGHT_USED + ACTIVITY_WEIGHT_USED = 100% (always)
✓ Σ Normalized_Weight[i] = 100% (for present types)
✓ Module score range: [0, 100]
```

---

**Last Updated**: January 17, 2025  
**Purpose**: Visual reference for dynamic weight system
