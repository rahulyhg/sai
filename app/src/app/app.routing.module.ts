import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SignInComponent } from './home/signin/signin.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { AuthGuard } from './core/auth/auth.guard';
import { SignInGuard } from './core/auth/signin.guard';
import { RegisterStudentComponent } from './register/register-student.component';
import { ContractComponent } from './register/contract/contract.component';
import { StudentReportComponent } from './report/student-reports/student-reports.component';
import { StudentComponent } from './student/student.component';
import { UnitClassesComponent } from './classes/unit-classes/unit-classes.component';
import { RegisterPresenceComponent } from './presence/register-presence/register-presence.component';
import { ManageMaterialComponent } from './material/manage-material/manage-material.component';
import { MaterialComponent } from './material/material.component';
import { AdmGuard } from './core/auth/adm.guard';
import { RegisterTeacherComponent } from './teacher/register-teacher/register-teacher.component';
import { LinkDisciplineComponent } from './teacher/link-discipline/link-discipline.component';
import { ReplyIbopeComponent } from './ibope/replay-ibope/reply-ibope.component';
import { ManageIbopeComponent } from './ibope/manage-ibope/manage-ibope.component';


const routes: Routes = [
    {
        path: 'signin',
        component: SignInComponent,
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
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'register/contract',
        component: ContractComponent,
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'report/students',
        component: StudentReportComponent,
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'student/:s',
        component: StudentComponent,
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'classes/unit-classes',
        component: UnitClassesComponent,
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'presence/register-presence',
        component: RegisterPresenceComponent,
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'material/manage-material',
        component: ManageMaterialComponent,
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'teacher/register-teacher',
        component: RegisterTeacherComponent,
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'teacher/link-discipline',
        component: LinkDisciplineComponent,
        canActivate: [AuthGuard, AdmGuard]
    },
    {
        path: 'material/material',
        component: MaterialComponent,
        canActivate: [AuthGuard]
    },
    {
        path: 'ibope/reply-ibope',
        component: ReplyIbopeComponent,
        canActivate: [AuthGuard]
    },
    {
        path: 'ibope/manage-ibope',
        component: ManageIbopeComponent,
        canActivate: [AuthGuard, AdmGuard]
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
