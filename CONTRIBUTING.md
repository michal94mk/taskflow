# Contributing to TaskFlow

Thank you for considering contributing to TaskFlow! This document outlines the process and guidelines for contributing.

## 🤝 How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](https://github.com/yourusername/taskflow/issues)
2. If not, create a new issue with:
   - Clear title and description
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots if applicable
   - Environment details (OS, PHP version, Node version)

### Suggesting Features

1. Check if the feature has been suggested in [Issues](https://github.com/yourusername/taskflow/issues)
2. Create a new issue with:
   - Clear description of the feature
   - Use cases and benefits
   - Possible implementation approach

### Pull Requests

1. Fork the repository
2. Create a feature branch from `develop`:
   ```bash
   git checkout develop
   git checkout -b feature/your-feature-name
   ```

3. Make your changes following our coding standards

4. Write or update tests if applicable

5. Commit your changes with clear messages:
   ```bash
   git commit -m "feat: add new feature"
   ```

6. Push to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```

7. Open a Pull Request to the `develop` branch

## 📝 Commit Message Convention

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

- `feat:` - New feature
- `fix:` - Bug fix
- `docs:` - Documentation changes
- `style:` - Code style changes (formatting, etc.)
- `refactor:` - Code refactoring
- `test:` - Adding or updating tests
- `chore:` - Maintenance tasks

Examples:
```
feat: add global search functionality
fix: resolve dark mode toggle issue
docs: update installation instructions
```

## 🎨 Coding Standards

### PHP (Laravel)

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Use type hints and return types
- Write PHPDoc comments for public methods
- Keep methods focused and small

Example:
```php
/**
 * Get all tasks for the authenticated user.
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getUserTasks(): Collection
{
    return Task::where('user_id', auth()->id())
        ->with(['project', 'taskStatus'])
        ->get();
}
```

### JavaScript/TypeScript (Vue)

- Use TypeScript for type safety
- Follow Vue 3 Composition API style
- Use `<script setup>` syntax
- Keep components focused and reusable
- Use proper TypeScript interfaces

Example:
```typescript
interface Task {
    id: number;
    title: string;
    description?: string;
}

const props = defineProps<{
    tasks: Task[];
}>();
```

### CSS (Tailwind)

- Use Tailwind utility classes
- Avoid custom CSS when possible
- Use consistent spacing scale
- Follow mobile-first approach

## 🧪 Testing

- Write tests for new features
- Ensure existing tests pass:
  ```bash
  php artisan test
  ```

- Run linting:
  ```bash
  npm run lint
  ```

## 🌿 Git Workflow

We use Git Flow:

1. `main` - Production-ready code
2. `develop` - Development branch (merge PRs here)
3. `feature/*` - Feature branches
4. `hotfix/*` - Urgent fixes

### Branch Naming

- Features: `feature/add-email-notifications`
- Fixes: `fix/resolve-login-issue`
- Hotfixes: `hotfix/critical-security-patch`

## 📋 Pull Request Checklist

Before submitting a PR, ensure:

- [ ] Code follows project coding standards
- [ ] Tests pass (`php artisan test`)
- [ ] Linting passes (`npm run lint`)
- [ ] Commits follow conventional commit format
- [ ] PR description clearly explains changes
- [ ] Documentation updated if needed
- [ ] No merge conflicts with `develop`

## 🔍 Code Review Process

1. Maintainers will review your PR
2. Address any requested changes
3. Once approved, your PR will be merged
4. Your contribution will be credited

## 💡 Development Setup

See [README.md](README.md) for detailed setup instructions.

Quick start:
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

## 📞 Questions?

- Open an issue for questions
- Join our discussions
- Contact maintainers

Thank you for contributing! 🎉

