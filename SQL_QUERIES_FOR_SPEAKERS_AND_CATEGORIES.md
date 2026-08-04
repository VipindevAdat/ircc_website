# SQL Queries to Check Speakers and Categories

Run these queries in your database to diagnose and fix the issues:

## 1. Check Team Categories Structure

```sql
-- See parent categories (should be: 1=Committee, 2=Speakers)
SELECT id, name, parent_id, status, sort_order 
FROM team_categories 
WHERE parent_id IS NULL 
ORDER BY id;
```

## 2. Check Sub-categories Under Committee (parent_id=1)

```sql
SELECT id, name, parent_id, status 
FROM team_categories 
WHERE parent_id = 1 
ORDER BY id;
```

## 3. Check Sub-categories Under Speakers (parent_id=2)

```sql
SELECT id, name, parent_id, status 
FROM team_categories 
WHERE parent_id = 2 
ORDER BY id;
```

## 4. Check All Teams and Their Categories

```sql
SELECT 
    t.id,
    t.name,
    t.designation,
    t.status,
    tc.name AS category_name,
    tc.parent_id,
    CASE 
        WHEN tc.parent_id = 1 THEN 'Committee'
        WHEN tc.parent_id = 2 THEN 'Speakers'
        ELSE 'Other'
    END AS category_type
FROM teams t
LEFT JOIN team_categories tc ON t.team_category_id = tc.id
ORDER BY tc.parent_id, tc.name, t.name;
```

## 5. Check Which Teams Are Showing on Committee Page (parent_id=1)

```sql
SELECT 
    t.id,
    t.name,
    t.designation,
    tc.name AS category_name,
    tc.parent_id
FROM teams t
LEFT JOIN team_categories tc ON t.team_category_id = tc.id
WHERE tc.parent_id = 1 AND t.status = 1
ORDER BY t.name;
```

## 6. Check Which Teams Should Show on Speakers Page (parent_id=2)

```sql
SELECT 
    t.id,
    t.name,
    t.designation,
    tc.name AS category_name,
    tc.parent_id
FROM teams t
LEFT JOIN team_categories tc ON t.team_category_id = tc.id
WHERE tc.parent_id = 2 AND t.status = 1
ORDER BY t.name;
```

## 7. Check Media Attachments for Teams

```sql
SELECT 
    t.id,
    t.name,
    m.id AS media_id,
    m.filename,
    m.directory,
    med.tag,
    m.disk
FROM teams t
LEFT JOIN mediables med ON med.mediable_id = t.id AND med.mediable_type = 'App\\Models\\Team'
LEFT JOIN media m ON med.media_id = m.id
WHERE t.status = 1
ORDER BY t.name;
```

## 8. Find Teams Without Photos

```sql
SELECT 
    t.id,
    t.name,
    t.designation,
    tc.name AS category_name
FROM teams t
LEFT JOIN team_categories tc ON t.team_category_id = tc.id
LEFT JOIN mediables med ON med.mediable_id = t.id 
    AND med.mediable_type = 'App\\Models\\Team' 
    AND med.tag = 'team'
WHERE t.status = 1 AND med.id IS NULL
ORDER BY t.name;
```

## 9. Fix: Move Speakers from Committee to Speakers Category

**First, check which teams need to be moved:**

```sql
-- Find teams that should be speakers but are in committee categories
SELECT 
    t.id,
    t.name,
    t.team_category_id,
    tc.name AS current_category,
    tc.parent_id
FROM teams t
LEFT JOIN team_categories tc ON t.team_category_id = tc.id
WHERE tc.parent_id = 1 
AND t.name LIKE '%speaker%'  -- Adjust this condition based on your data
ORDER BY t.name;
```

**Then, if you need to move them, find the correct speaker category ID:**

```sql
-- Get speaker category IDs
SELECT id, name, parent_id 
FROM team_categories 
WHERE parent_id = 2 AND status = 1;
```

**Update the team category (replace X with correct speaker category ID and Y with team ID):**

```sql
-- Example: Move team ID 5 to speaker category ID 10
-- UPDATE teams SET team_category_id = 10 WHERE id = 5;
```

## 10. Create Missing Speaker Category (if needed)

```sql
-- Check if parent category "Speakers" exists (parent_id should be NULL, id should be 2)
SELECT * FROM team_categories WHERE id = 2 OR name LIKE '%Speaker%';

-- If it doesn't exist, create it:
-- INSERT INTO team_categories (name, parent_id, status, sort_order, created_at, updated_at)
-- VALUES ('Speakers', NULL, 1, 2, NOW(), NOW());

-- Then create a sub-category under Speakers:
-- INSERT INTO team_categories (name, parent_id, status, sort_order, created_at, updated_at)
-- VALUES ('Keynote Speakers', 2, 1, 1, NOW(), NOW());
```

## Notes:

- **Committee categories** should have `parent_id = 1`
- **Speaker categories** should have `parent_id = 2`
- Teams assigned to categories with `parent_id = 1` will show on the Committee page
- Teams assigned to categories with `parent_id = 2` will show on the Speakers page
- Photos are stored in the `media` table and linked via `mediables` table with tag `'team'`
