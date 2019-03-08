import { NgModule } from '@angular/core';
import { ClassesComponent } from './classes.component';
import { CommonModule } from '@angular/common';
import { UnitClassesComponent } from './unit-classes/unit-classes.component';
import { MatSelectModule, MatButtonModule, MatFormFieldModule, MatCardModule } from '@angular/material';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

@NgModule({
    declarations: [
        ClassesComponent,
        UnitClassesComponent
    ],
    imports: [
        CommonModule,
        FormsModule,
        ReactiveFormsModule,
        RouterModule,
        MatSelectModule,
        MatButtonModule,
        MatFormFieldModule,
        MatCardModule
    ]
})

export class ClassesModule { }
