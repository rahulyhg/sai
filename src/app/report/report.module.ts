import { NgModule } from '@angular/core';
import { StudentReportComponent } from './student-reports/student-reports.component';
import { AlertModule } from '../shared/services/alert/alert.module';
import { CommonModule } from '@angular/common';
import { MatTableModule, MatSelectModule, MatFormFieldModule, MatButtonModule, MatIconModule } from '@angular/material';
import { RouterModule } from '@angular/router';

@NgModule({
    declarations: [
        StudentReportComponent
    ],
    imports: [
        CommonModule,
        AlertModule,
        MatTableModule,
        RouterModule,
        MatSelectModule,
        MatFormFieldModule,
        MatButtonModule,
        MatIconModule
    ]
})

export class ReportModule { }
