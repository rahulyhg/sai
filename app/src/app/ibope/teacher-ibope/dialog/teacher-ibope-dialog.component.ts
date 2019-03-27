import { Component, Inject, Optional } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material';
import { TeacherIbope } from '../teacher-ibope';

@Component({
    templateUrl: './teacher-ibope-dialog.component.html',
    styleUrls: ['./teacher-ibope-dialog.component.scss']
})

export class TeacherIbopeDialogComponent {



    constructor(
        @Optional() public dialogRef: MatDialogRef<TeacherIbopeDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: TeacherIbope) {}

    onNoClick() {
        this.dialogRef.close();
    }
}
