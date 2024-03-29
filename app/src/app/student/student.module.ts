import { NgModule } from '@angular/core';
import { StudentComponent } from './student.component';
import { CommonModule } from '@angular/common';
import { MatCardModule, MatButtonModule, MatDividerModule, MatChipsModule, MatIconModule } from '@angular/material';
import { RouterModule } from '@angular/router';
import { RegisterModule } from '../register/register.module';

@NgModule({
    declarations: [
        StudentComponent
    ],
    imports: [
        RegisterModule,
        CommonModule,
        RouterModule,
        MatCardModule,
        MatButtonModule,
        MatDividerModule,
        MatChipsModule,
        MatIconModule
    ]
})

export class StudentModule {}
