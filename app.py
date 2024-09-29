from flask import Flask, render_template, redirect, url_for, flash, request
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)
app.config['SECRET_KEY'] = 'supersecretkey'
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///crowdfunding.db'

db = SQLAlchemy(app)
login_manager = LoginManager()
login_manager.init_app(app)
login_manager.login_view = 'login'

from models import User, Project, Donation

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

@app.route('/')
def index():
    projects = Project.query.all()
    return render_template('index.html', projects=projects)

# User registration
@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        email = request.form.get('email')
        password = request.form.get('password')
        hashed_password = generate_password_hash(password, method='sha256')

        new_user = User(email=email, password=hashed_password)
        db.session.add(new_user)
        db.session.commit()

        flash('Account created successfully!', 'success')
        return redirect(url_for('login'))

    return render_template('register.html')

# User login
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        email = request.form.get('email')
        password = request.form.get('password')
        user = User.query.filter_by(email=email).first()

        if user and check_password_hash(user.password, password):
            login_user(user)
            return redirect(url_for('dashboard'))
        else:
            flash('Login failed. Check email and password.', 'danger')

    return render_template('login.html')

# Logout
@app.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('index'))

# Dashboard for creating projects and viewing donations
@app.route('/dashboard')
@login_required
def dashboard():
    projects = Project.query.filter_by(user_id=current_user.id).all()
    donations = Donation.query.filter_by(user_id=current_user.id).all()
    return render_template('dashboard.html', projects=projects, donations=donations)

# Create a new crowdfunding project
@app.route('/projects/new', methods=['GET', 'POST'])
@login_required
def create_project():
    if request.method == 'POST':
        title = request.form.get('title')
        description = request.form.get('description')
        goal = request.form.get('goal')

        new_project = Project(title=title, description=description, goal=goal, user_id=current_user.id)
        db.session.add(new_project)
        db.session.commit()

        flash('Project created successfully!', 'success')
        return redirect(url_for('dashboard'))

    return render_template('projects.html')

# Donate to a project
@app.route('/donate/<int:project_id>', methods=['GET', 'POST'])
@login_required
def donate(project_id):
    project = Project.query.get_or_404(project_id)
    if request.method == 'POST':
        amount = request.form.get('amount')
        new_donation = Donation(amount=amount, project_id=project_id, user_id=current_user.id)
        project.raised += float(amount)

        db.session.add(new_donation)
        db.session.commit()

        flash('Thank you for your donation!', 'success')
        return redirect(url_for('index'))

    return render_template('donate.html', project=project)

if __name__ == '__main__':
    app.run(debug=True)
