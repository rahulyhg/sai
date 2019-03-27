import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatCardModule, MatButtonModule, MatFormFieldModule,
    MatInputModule, MatTableModule, MatIconModule, MatSelectModule, MatTabsModule } from '@angular/material';
import { RegisterTeacherComponent } from './register-teacher/register-teacher.component';
import { LinkDisciplineComponent } from './link-discipline/link-discipline.component';
import { DragDropModule } from '@angular/cdk/drag-drop';

@NgModule({
    declarations: [
        RegisterTeacherComponent,
        LinkDisciplineComponent
    ],
    imports: [
        CommonModule,
        ReactiveFormsModule,
        FormsModule,
        MatCardModule,
        MatButtonModule,
        MatFormFieldModule,
        MatInputModule,
        MatTableModule,
        MatIconModule,
        MatSelectModule,
        DragDropModule,
        MatTabsModule
    ]
})

export class TeacherModule {}
