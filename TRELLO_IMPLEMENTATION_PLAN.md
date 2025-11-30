# SmartTaskBoard - Trello-подобный функционал: План реализации

## 📊 Статус: Текущее состояние

### ✅ Уже реализовано (80% базовых функций)
- Канбан-доска с drag-drop
- Множественные доски и колонки
- Полная система тегов с цветами
- Команды (Teams) с управлением ролями
- Комментарии к задачам
- Вложения (файлы)
- Чек-листы в задачах
- Множественные исполнители
- Отслеживание времени (оценка и потрачено)
- Темная тема и многоязычность (EN/RU)
- Поиск и фильтрация
- Экспорт (JSON, CSV, PDF)
- Система уведомлений (архитектура готова)
- Логирование активности (архитектура готова)

### ⏳ Требуется реализовать (20% расширенных функций)

---

## 🎯 Приоритизированный план

### ФАЗА 1: КРИТИЧЕСКИ ВАЖНОЕ (неделя 1-2)
Функции, которые значительно улучшат пользовательский опыт

#### 1.1 Улучшение UI карточек на доске ⭐⭐⭐
**Приоритет:** ВЫСОКИЙ (видно всем пользователям)

**Что нужно:**
- [ ] Обложки карточек (изображения)
- [ ] Цветовые индикаторы статуса выполнения
- [ ] Значки с количеством комментариев и вложений
- [ ] Аватарки исполнителей на карточке
- [ ] Визуальный индикатор просрочки (красный цвет)
- [ ] Показ дедлайна прямо на карточке
- [ ] Меню быстрых действий (контекстное меню)

**Файлы для изменения:**
- `resources/views/components/task-card.blade.php` - обновить отображение
- `app/Models/Task.php` - добавить поле `cover_image`
- Migration для добавления `cover_image` в tasks

**Миграция БД:**
```sql
ALTER TABLE tasks ADD COLUMN cover_image VARCHAR(255) NULL AFTER description;
```

**Время реализации:** 3-4 часа

---

#### 1.2 Расширенная фильтрация и поиск ⭐⭐⭐
**Приоритет:** ВЫСОКИЙ (улучшает навигацию)

**Что нужно:**
- [ ] Фильтр по диапазону дат (От...До)
- [ ] Фильтр "Просроченные"
- [ ] Фильтр "Срок в течение X дней"
- [ ] Фильтр "Нет дедлайна"
- [ ] Фильтр по статусу выполнения (выполненные/невыполненные)
- [ ] Сохранение последних использованных фильтров
- [ ] Быстрые фильтры в виде кнопок
- [ ] Расширенная история поиска

**Файлы для изменения:**
- `resources/views/board.blade.php` - добавить новые фильтры в UI
- `app/Http/Controllers/BoardController.php` - логика фильтрации

**Время реализации:** 3-4 часа

---

#### 1.3 Контекстное меню карточек ⭐⭐
**Приоритет:** СРЕДНИЙ (удобство)

**Что нужно:**
- [ ] Правый клик на карточку = контекстное меню
- [ ] Опции:
  - Открыть карточку (уже есть)
  - Изменить метки
  - Изменить участников
  - Изменить обложку
  - Изменить дедлайн
  - Переместить в другую колонку
  - Скопировать карточку
  - Добавить в избранное
  - Архивировать

**Файлы для изменения:**
- `resources/views/components/task-card.blade.php` - добавить контекстное меню

**Время реализации:** 2-3 часа

---

### ФАЗА 2: ВАЖНОЕ ФУНКЦИОНАЛЬНО (неделя 2-3)

#### 2.1 Архивирование карточек и досок ⭐⭐
**Приоритет:** СРЕДНИЙ

**Что нужно:**
- [ ] Поле `archived` в таблице `tasks`
- [ ] Поле `archived_at` в таблице `tasks`
- [ ] Поле `archived` в таблице `boards`
- [ ] API для архивирования/восстановления
- [ ] Фильтр "Показать архивированные"
- [ ] Отдельное представление для архива

**Миграция БД:**
```sql
ALTER TABLE tasks ADD COLUMN archived BOOLEAN DEFAULT FALSE;
ALTER TABLE tasks ADD COLUMN archived_at TIMESTAMP NULL;
ALTER TABLE boards ADD COLUMN archived BOOLEAN DEFAULT FALSE;
ALTER TABLE boards ADD COLUMN archived_at TIMESTAMP NULL;
```

**Время реализации:** 3 часа

---

#### 2.2 Повторяющиеся задачи (Recurring Tasks) ⭐⭐
**Приоритет:** СРЕДНИЙ (очень полезно для регулярных работ)

**Что нужно:**
- [ ] Таблица `recurring_tasks` с полями:
  - `task_id` (FK)
  - `frequency` (enum: daily, weekly, biweekly, monthly, yearly)
  - `next_occurrence` (datetime)
  - `last_created_at` (datetime)
- [ ] UI для установки повтора при создании задачи
- [ ] Крон-джоб для создания новых экземпляров
- [ ] Отметка "Это повторяющаяся задача" на карточке

**Миграция БД:**
```sql
CREATE TABLE recurring_tasks (
  id BIGINT PRIMARY KEY,
  task_id BIGINT UNIQUE NOT NULL,
  frequency ENUM('daily', 'weekly', 'biweekly', 'monthly', 'yearly') NOT NULL,
  next_occurrence TIMESTAMP NOT NULL,
  last_created_at TIMESTAMP NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);
```

**Время реализации:** 4-5 часов

---

#### 2.3 Зависимости между задачами ⭐⭐
**Приоритет:** СРЕДНИЙ (для сложных проектов)

**Что нужно:**
- [ ] Таблица `task_dependencies` (task_id, depends_on_task_id, type)
- [ ] Типы: "blocks", "blocked_by", "relates_to", "duplicates"
- [ ] Визуальные связи между карточками на доске
- [ ] Валидация (нельзя завершить если есть зависимые)
- [ ] Отображение связей в модальном окне задачи

**Миграция БД:**
```sql
CREATE TABLE task_dependencies (
  id BIGINT PRIMARY KEY,
  task_id BIGINT NOT NULL,
  depends_on_task_id BIGINT NOT NULL,
  type ENUM('blocks', 'blocked_by', 'relates_to', 'duplicates') DEFAULT 'blocks',
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE KEY unique_dependency (task_id, depends_on_task_id),
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  FOREIGN KEY (depends_on_task_id) REFERENCES tasks(id) ON DELETE CASCADE
);
```

**Время реализации:** 5-6 часов

---

### ФАЗА 3: УЛУЧШЕНИЕ ОПЫТА (неделя 3-4)

#### 3.1 История и активность ⭐
**Приоритет:** НИЗКИЙ (но красиво)

**Что нужно:**
- [ ] Расширенная история всех действий на доске
- [ ] Временная шкала изменений
- [ ] Откат действий (undo) для администратора
- [ ] Сво форматированная активность:
  - "Иван создал задачу 'Тестирование'"
  - "Мария добавила Петра исполнителем"
  - "Задача перемещена в 'Готово'"
- [ ] Уведомления об изменениях в реальном времени

**Использовать:** Уже есть таблица `activity_logs`

**Время реализации:** 3-4 часа

---

#### 3.2 Публичные ссылки на доски ⭐
**Приоритет:** НИЗКИЙ (но полезно для демонстрации)

**Что нужно:**
- [ ] Таблица `board_shares`:
  - `id`, `board_id`, `uuid`, `permissions` (enum: view, edit)
  - `expires_at` (опционально)
  - `created_by`, `created_at`
- [ ] API для создания/удаления ссылки
- [ ] Публичное представление доски (read-only по умолчанию)
- [ ] Copy to clipboard для ссылки

**Миграция БД:**
```sql
CREATE TABLE board_shares (
  id BIGINT PRIMARY KEY,
  board_id BIGINT NOT NULL,
  uuid VARCHAR(36) UNIQUE NOT NULL,
  permissions ENUM('view', 'edit') DEFAULT 'view',
  expires_at TIMESTAMP NULL,
  created_by BIGINT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (board_id) REFERENCES boards(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);
```

**Время реализации:** 4-5 часов

---

#### 3.3 Уведомления и напоминания ⭐
**Приоритет:** НИЗКИЙ (требует email/push)

**Что нужно:**
- [ ] Email-уведомления о дедлайнах (за день, за час)
- [ ] Напоминания о пропущенных сроках
- [ ] Pusher Real-time уведомления (Pusher уже настроен!)
- [ ] Статус "прочитано" для уведомлений
- [ ] Маршалл настройки уведомлений (по ролям)

**Использовать:** Структура уже есть в `notifications` таблице

**Время реализации:** 4-5 часов

---

### ФАЗА 4: РАСШИРЕННЫЕ ВИДЫ (неделя 4-5)

#### 4.1 Альтернативные виды досок ⭐
**Приоритет:** НИЗКИЙ (но интересно)

**Что нужно:**
- [ ] Вид "Таблица" (список всех задач)
- [ ] Вид "Календарь" (задачи по датам)
- [ ] Вид "Временная шкала/Gantt"
- [ ] Вид "Диаграмма" (статистика)
- [ ] Переключение между видами

**Компоненты:**
- Таблица: Bootstrap Table или аналог
- Календарь: Vue/Alpine календарный компонент
- Gantt: dhtmlxGantt или similar
- Диаграмма: Chart.js

**Время реализации:** 6-8 часов

---

#### 4.2 Шаблоны досок и карточек ⭐
**Приоритет:** НИЗКИЙ

**Что нужно:**
- [ ] Сохранение доски как шаблон
- [ ] Создание доски из шаблона
- [ ] Шаблоны карточек для быстрого создания
- [ ] Галерея встроенных шаблонов (по типам проектов)

**Таблицы:**
```sql
CREATE TABLE board_templates (
  id BIGINT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  thumbnail VARCHAR(255),
  created_by BIGINT NOT NULL,
  is_public BOOLEAN DEFAULT FALSE,
  data JSON, -- Структура columns и tasks
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

**Время реализации:** 4-5 часов

---

---

## 📅 РЕКОМЕНДУЕМЫЙ ГРАФИК

| Неделя | Фаза | Фокус | Часов |
|--------|------|-------|-------|
| 1-2 | ФАЗА 1 | Критически важное | 8-12 |
| 2-3 | ФАЗА 2 | Функциональное | 16-20 |
| 3-4 | ФАЗА 3 | Опыт | 11-14 |
| 4-5 | ФАЗА 4 | Расширения | 14-18 |

**Всего:** 49-64 часа разработки

---

## 🏗️ АРХИТЕКТУРНЫЕ ИЗМЕНЕНИЯ

### Новые Models:
```php
// app/Models/RecurringTask.php
// app/Models/TaskDependency.php
// app/Models/BoardShare.php
// app/Models/BoardTemplate.php
```

### Новые Controllers:
```php
// app/Http/Controllers/RecurringTaskController.php
// app/Http/Controllers/TaskDependencyController.php
// app/Http/Controllers/BoardShareController.php
// app/Http/Controllers/TemplateController.php
// app/Http/Controllers/ArchiveController.php
```

### Новые Routes:
```
// Все маршруты добавить в api группу в routes/web.php
POST   /api/tasks/{task}/dependencies
DELETE /api/tasks/{task}/dependencies/{dep}
POST   /api/tasks/{task}/recurring
DELETE /api/tasks/{task}/recurring

POST   /api/boards/{board}/share
DELETE /api/shares/{share}
GET    /share/{uuid} - public route (без auth middleware)

POST   /api/tasks/{task}/archive
POST   /api/boards/{board}/archive
GET    /api/archive/tasks
GET    /api/archive/boards
```

### Новые Events:
```php
// app/Events/RecurringTaskCreated.php
// app/Events/TaskArchivedEvent.php
// app/Events/DependencyBrokenEvent.php
// app/Events/TaskSharedEvent.php
```

### Новые Jobs (Queues):
```php
// app/Jobs/CreateRecurringTaskInstances.php - crон-джоб
// app/Jobs/SendDeadlineReminders.php - напоминания
// app/Jobs/ProcessNotifications.php
```

---

## 🧪 ТЕСТИРОВАНИЕ

### Unit Tests (обязательно):
- RecurringTask creation & validation
- TaskDependency constraints
- Archive/restore operations
- Filter logic

### Feature Tests:
- Drag-drop с новыми features
- Recurring task creation
- Dependency validation
- Public shares access

### E2E Tests (Cypress/Playwright):
- Полный workflow создания задачи с повтором
- Фильтрация по всем параметрам
- Архивирование и восстановление

---

## 🔒 БЕЗОПАСНОСТЬ

- ✅ Авторизация на все endpoint'ы (проверить access к board/task)
- ✅ Валидация зависимостей (circular dependency check)
- ✅ Сохранение истории действий для всех изменений
- ✅ Шифрование публичных ссылок (UUID v4)
- ✅ Rate limiting на API

---

## 📱 МОБИЛЬНОСТЬ

- Все новые функции должны работать на мобильных устройствах
- Протестировать на iPhone/Android
- Drag-drop на мобильных (touch events)

---

## 📊 МЕТРИКИ УСПЕХА

После реализации:
- ✅ Функциональность на 95% паритета с Trello
- ✅ Performance: <200ms на все API запросы
- ✅ Поддержка 1000+ задач без лага
- ✅ 99.9% uptime с proper error handling
- ✅ Все функции покрыты тестами (>80% coverage)

---

## 💡 QUICK WINS (можно сделать в первую очередь)

1. **Контекстное меню** (2 часа) - красиво, видно сразу
2. **Обложки карточек** (1 час) - просто, визуально улучшает
3. **Расширенная фильтрация** (3 часа) - очень полезно
4. **Архивирование** (3 часа) - simple but powerful
5. **История активности** (3 часа) - интересно для всех

**Итого Quick Wins: 12 часов = 1-2 дня работы**

---

## 🚀 СЛЕДУЮЩИЕ ШАГИ

1. **Выбери приоритеты** - какой функционал более важен для твоих пользователей?
2. **Создай backlog в JIRA/Trello** - распределение по спринтам
3. **Начни с ФАЗЫ 1** - видимые улучшения первыми
4. **Регулярно тестируй** - особенно drag-drop и фильтры
5. **Собирай feedback** - пользователи подскажут, что важнее

---

**Готов начать реализацию? Предлагаю стартовать с ФАЗЫ 1 (контекстное меню + обложки + расширенные фильтры) 🚀**
