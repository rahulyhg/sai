import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { ReplyIbopeComponent } from './replay-ibope/reply-ibope.component';
import { MatFormFieldModule, MatTabsModule, MatButtonModule, MatExpansionModule,
    MatInputModule, MatIconModule, MatRadioModule, MatCardModule, MatDialogModule,
    MatTableModule, MatBadgeModule, MatSelectModule, MatSlideToggleModule, MatDividerModule } from '@angular/material';
import { TeacherIbopeComponent } from './teacher-ibope/teacher-ibope.component';
import { TeacherIbopeDialogComponent } from './teacher-ibope/dialog/teacher-ibope-dialog.component';
import { ManageIbopeComponent } from './manage-ibope/manage-ibope.component';
import { RegisterModule } from '../register/register.module';


@NgModule({
    declarations: [
        ReplyIbopeComponent,
        TeacherIbopeComponent,
        TeacherIbopeDialogComponent,
        ManageIbopeComponent
    ],
    entryComponents: [TeacherIbopeDialogComponent],
    imports: [
        CommonModule,
        ReactiveFormsModule,
        FormsModule,
        MatTabsModule,
        MatFormFieldModule,
        MatButtonModule,
        MatExpansionModule,
        MatInputModule,
        MatIconModule,
        MatRadioModule,
        MatCardModule,
        MatDialogModule,
        MatTableModule,
        MatBadgeModule,
        MatSelectModule,
        MatSlideToggleModule,
        RegisterModule, // INCLUIDO PARA UTILIZAR APENAS O PIPE MONTHPT ;-;
        MatDividerModule

    ], exports: [TeacherIbopeDialogComponent]
})

export class IbopeModule {}
