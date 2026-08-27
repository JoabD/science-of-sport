# Assessment Requirements Checklist

## Functional
- [X] User authentication with roles (at least 2 distinct roles)
- [X] Models using Eloquent ORM with relationships (no raw queries)
- [X] At least one AJAX function (e.g. pagination without page reload)
- [X] CRUD that saves the info from the "Golf Classic Tournament 2025" post
- [X] Business logic in classes (Services/Actions), NOT in controllers

## Non-functional
- [X] Documented code (comments, PHPDoc on key methods)
- [X] No AI-generated code (you must be able to explain every decision)
- [X] Design can be inspired by the colors/fonts of the original post
- [X] Use of Bootstrap or another library allowed for the front-end

## Deliverables
- [ ] Deployed site URL
- [ ] Repository URL (GitHub or other)
- [X] Username and password for each role [Admin, Regular]
- [Admin - "password123"]
- [Regular - "password123"]
- [X] Access to files/repo for review

## Self-check questions
1. **ORM (20%)** — Do you have at least one relationship (`hasMany`, `belongsTo`, etc.) between two models, or is everything in a single flat table?
2. **Auth (20%)** — Does a user with Role A see/do something different from a user with Role B? Roles that exist in the DB but don't change routes/UI don't count as implemented.
3. **Classes outside controllers (Structure/Code)** — Open your main controller: does any method have more than 10-15 lines of business logic (calculations, complex validation, conditional saving)? If so, that logic should live in a separate Service/Action class.
4. **AJAX (10%)** — Open DevTools → Network → reload the page with the paginator. Do you see an XHR/fetch request without a full page reload?
5. **Data saved** — Compare your table field by field against the real post: title, date, venue, price, description. Is anything missing that appears on the page?
