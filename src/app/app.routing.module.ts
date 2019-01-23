import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SignInComponent } from './home/signin/signin.component';
import { SignUpComponent } from './home/sigup/signup.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { AuthGuard } from './core/auth/auth.guard';
import { SignInGuard } from './core/auth/signin.guard';
import { RegisterStudentComponent } from './register/register-student.component';
import { ContractComponent } from './register/contract/contract.component';
import { StudentReportComponent } from './report/student-reports/student-reports.component';
import { StudentComponent } from './student/student.component';


const routes: Routes = [
    {
        path: 'signin',
        component: SignInComponent,
        canActivate: [SignInGuard]
    },
    {
        path: 'signup',
        component: SignUpComponent,
        canActivate: [SignInGuard]
    },
    {
        path: 'dashboard',
        component: DashboardComponent,
        canActivate: [AuthGuard]
    },
    {
        path: '',
        component: SignInComponent,
        canActivate: [SignInGuard, AuthGuard]
    },
    {
        path: 'register/student',
        component: RegisterStudentComponent,
        canActivate: [AuthGuard]
    },
    {
        path: 'register/contract',
        component: ContractComponent,
        canActivate: [AuthGuard]
    },
    {
        path: 'report/students',
        component: StudentReportComponent,
        canActivate: [AuthGuard]
    },
    {
        path: 'student/:s',
        component: StudentComponent,
        canActivate: [AuthGuard]
    },

];

export const appRouting = RouterModule.forRoot(routes);

@NgModule({
    imports: [
        RouterModule.forRoot(routes, { useHash: true})
    ],
    exports: [RouterModule]
})
export class AppRoutingModule { }
