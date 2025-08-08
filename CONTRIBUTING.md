# Contributing to Task Management System

Thank you for your interest in contributing to the Task Management System! This document provides guidelines and information for contributors.

## 🤝 How to Contribute

### Reporting Bugs
- Use the GitHub issue tracker
- Provide detailed information about the bug
- Include steps to reproduce the issue
- Mention your environment (OS, PHP version, Laravel version)

### Suggesting Features
- Use the GitHub issue tracker
- Describe the feature in detail
- Explain why this feature would be useful
- Consider the impact on existing functionality

### Code Contributions
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests if applicable
5. Commit your changes (`git commit -m 'Add some amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## 🛠️ Development Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Git

### Local Development
1. Clone your fork
2. Install dependencies: `composer install && npm install --legacy-peer-deps`
3. Copy `.env.example` to `.env` and configure
4. Generate app key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Build assets: `npm run build`
7. Start development server: `php artisan serve`

## 📋 Coding Standards

### PHP/Laravel
- Follow PSR-12 coding standards
- Use Laravel conventions
- Write meaningful commit messages
- Add proper documentation
- Include type hints where possible

### JavaScript/Vue
- Follow Vue.js style guide
- Use ES6+ features
- Maintain consistent formatting
- Add comments for complex logic

### CSS/Tailwind
- Use Tailwind utility classes
- Maintain responsive design
- Follow consistent naming conventions
- Keep styles organized

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter TestName

# Run with coverage
php artisan test --coverage
```

### Writing Tests
- Write tests for new features
- Ensure existing tests pass
- Use descriptive test names
- Follow AAA pattern (Arrange, Act, Assert)

## 📝 Documentation

### Code Documentation
- Add PHPDoc comments for classes and methods
- Document complex business logic
- Update README.md for new features
- Keep changelog updated

### API Documentation
- Document new API endpoints
- Include request/response examples
- Update API documentation

## 🔒 Security

### Security Guidelines
- Never commit sensitive information
- Validate all user inputs
- Use Laravel's built-in security features
- Follow OWASP guidelines
- Report security issues privately

### Security Issues
If you discover a security vulnerability, please:
1. **DO NOT** create a public issue
2. Email the maintainers privately
3. Provide detailed information
4. Allow time for assessment and fix

## 🚀 Pull Request Guidelines

### Before Submitting
- Ensure all tests pass
- Update documentation if needed
- Follow coding standards
- Test on different environments
- Check for breaking changes

### PR Description
- Describe the changes clearly
- Link related issues
- Include screenshots for UI changes
- Mention any breaking changes
- List new dependencies

### Review Process
- Maintainers will review your PR
- Address feedback promptly
- Keep discussions constructive
- Be patient with the review process

## 🏷️ Issue Labels

We use the following labels:
- `bug` - Something isn't working
- `enhancement` - New feature or request
- `documentation` - Improvements or additions to documentation
- `good first issue` - Good for newcomers
- `help wanted` - Extra attention is needed
- `question` - Further information is requested

## 📞 Getting Help

### Questions and Support
- Check existing issues and discussions
- Review documentation
- Ask in GitHub discussions
- Be specific about your problem

### Community Guidelines
- Be respectful and inclusive
- Help others when possible
- Follow the code of conduct
- Provide constructive feedback

## 🎯 Areas for Contribution

### High Priority
- Bug fixes
- Security improvements
- Performance optimizations
- Documentation updates

### Medium Priority
- New features
- UI/UX improvements
- Test coverage
- Code refactoring

### Low Priority
- Cosmetic changes
- Minor optimizations
- Additional examples

## 📄 License

By contributing to this project, you agree that your contributions will be licensed under the MIT License.

## 🙏 Recognition

Contributors will be recognized in:
- Project README
- Release notes
- Contributor hall of fame

Thank you for contributing to the Task Management System! 🎉
