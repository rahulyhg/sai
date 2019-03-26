import { Component, OnInit } from '@angular/core';
import { TeacherService } from '../teacher.service';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { UserService } from 'src/app/user/user.service';
import { ResponseApi } from 'src/app/core/response-api';
import { Teacher } from '../teacher';
import { MatTableDataSource } from '@angular/material';

@Component({
    templateUrl: './register-teacher.component.html'
})

export class RegisterTeacherComponent implements OnInit {

    teacherForm: FormGroup;
    displayedColumns: string[];
    teachers: Teacher[];
    dataSource;

    constructor(
        private teacherService: TeacherService,
        private alertService: AlertService,
        private formBuilder: FormBuilder,
        private userService: UserService) {}

    ngOnInit() {
        this.teacherForm = this.formBuilder.group({
            name: ['', Validators.required],
            email: ['', [ Validators.required, Validators.email ]]
        });
        this.displayedColumns = [
            'name',
            'email',
            'remove'
        ];
        this.getTeachers(this.userService.getUserUnit());
    }

    registerTeacher() {
        if (this.teacherForm.valid && !this.teacherForm.pending) {
            const name  = this.teacherForm.get('name').value;
            const email = this.teacherForm.get('email').value;
            const unit  = this.userService.getUserUnit();

            this.teacherService.registerTeacher(name, email, unit)
                .subscribe( res => {
                    const response = res.body as ResponseApi;

                    if (!response.error) {

                        this.alertService.success('Professor(a) ' + name + ' cadastrado com sucesso');
                        this.teacherForm.reset();
                        this.getTeachers(this.userService.getUserUnit());

                    } else {
                        this.alertService.error(response.error);
                    }

                }, err => this.alertService.error('Houve um erro ao cadastrar o professor. Falha na comunicação com a API'));
        }
    }

    private getTeachers(unit: number) {
        this.teacherService.getTeachersUnit(unit)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    this.teachers = response.data as Teacher[];
                    this.dataSource = new MatTableDataSource(this.teachers);

                } else {
                    this.alertService.error(response.error);
                    this.dataSource = null;
                    this.teachers = null;
                }

            }, err => this.alertService.error('Houve um erro ao buscar os professores. Falha na comunicação com a API'));
    }

    removeTeacher(teacherId: number) {
        if (confirm('Deseja realmente remover o professor?')) {
            this.teacherService.removeTeacher(teacherId)
                .subscribe( res => {
                    const response = res.body as ResponseApi;

                    if (!response.error) {

                        this.alertService.success('Professor removido com sucesso');
                        this.getTeachers(this.userService.getUserUnit());
                    } else {
                        this.alertService.error(response.error);
                    }
                }, err => this.alertService.error('Houve um erro ao remover o professor. Falha na comunicação com a API'));
        }
    }
}
