import { Component, OnInit } from '@angular/core';
import { AlertService } from 'src/app/shared/services/alert/alert.service';
import { TeacherService } from '../teacher.service';
import { Teacher } from '../teacher';
import { Discipline } from 'src/app/material/discipline';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Class } from 'src/app/classes/class';
import { ClassesService } from 'src/app/classes/classes.service';
import { ResponseApi } from 'src/app/core/response-api';
import { UserService } from 'src/app/user/user.service';
import { MaterialService } from 'src/app/material/material.service';
import { CdkDragDrop, moveItemInArray, transferArrayItem } from '@angular/cdk/drag-drop';
import { TeacherLink } from './teacherLink';
import { MatTableDataSource } from '@angular/material';

@Component({
    templateUrl: 'link-discipline.component.html',
    styleUrls: ['link-discipline.component.scss']
})

export class LinkDisciplineComponent implements OnInit {

    teachers: Teacher[];
    disciplines: Discipline[];
    linkTeacherForm: FormGroup;
    classesOptions: Class[];
    selectedClasses: Class[];

    // abaixo sao usados no relatorio de vinculos
    unitClasses: Class[];
    displayedColumns: string[];
    dataSource;
    teachersLink: TeacherLink[];

    constructor(
        private alertService: AlertService,
        private teacherService: TeacherService,
        private formBuilder: FormBuilder,
        private classesService: ClassesService,
        private userService: UserService,
        private materialService: MaterialService) {}

    ngOnInit() {
        this.linkTeacherForm = this.formBuilder.group({
            teacher: ['', Validators.required],
            discipline: ['', Validators.required],
            classes: []
        });

        this.classesOptions   = [];
        this.selectedClasses  = [];
        this.getClasses(this.userService.getUserUnit());
        this.getDisciplines();
        this.getTeachers(this.userService.getUserUnit());

        this.displayedColumns = [
            'teacher',
            'discipline',
            'class',
            'remove'
        ];

        this.getUnitClasses(this.userService.getUserUnit());
        this.getTeachersLink(this.userService.getUserUnit());
    }

    getClasses(unitId: number) {
        this.classesService.getUnitClasses(unitId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.classesOptions = response.data as Class[];
                } else {
                    this.alertService.error(response.error);
                }

            }, err => this.alertService.error('Houve um erro ao buscar as turmas. Falha de comunicação com a API'));
    }

    getDisciplines() {
        this.materialService.getDisciplines()
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    this.disciplines = response.data as Discipline[];
                } else {
                    this.alertService.error(response.error);
                }

            }, err => this.alertService.error('Houve um erro ao buscar as disciplinas. Falha na comunicação com a API'));
    }

    getTeachers(unit: number) {
        this.teacherService.getTeachersUnit(unit)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.teachers = response.data as Teacher[];
                } else {
                    this.alertService.error(response.error);
                }

            }, err => this.alertService.error('Houve um erro ao buscar os professores cadastrados. Falha na comunicação com a API'));
    }

    drop(event: CdkDragDrop<Class[]>) {

        if (event.previousContainer === event.container) {
          moveItemInArray(event.container.data, event.previousIndex, event.currentIndex);
        } else {
          transferArrayItem(event.previousContainer.data,
                            event.container.data,
                            event.previousIndex,
                            event.currentIndex);
        }
    }

    submit() {
        if (!this.linkTeacherForm.errors && this.selectedClasses.length > 0) {

            let classes = '';

            this.selectedClasses.map(c => {
                classes += c.id + ',';
            });

            classes = classes.slice(0, -1);
            this.linkTeacherForm.get('classes').setValue(classes);

            this.teacherService.linkDiscipline(this.linkTeacherForm.getRawValue())
                .subscribe( res => {
                    const response = res.body as ResponseApi;

                    if (!response.error) {
                        this.alertService.success('Vínculo registrado co sucesso!');
                        this.getTeachersLink(this.userService.getUserUnit());
                        this.linkTeacherForm.reset();
                    } else {
                        this.alertService.error(response.error);
                    }

                }, err => this.alertService.error('Houve um erro ao registrar o vínculo. Falha na comunicação com a API'));

        } else {
            this.alertService.warning('Selecione pelomenos uma turma para vincular o material!');
        }
    }

    // ======== relatorio vinculos metodos ============ //

    applyFilter(filter) {
        this.dataSource.filter = filter;
    }

    private getUnitClasses(unitId: number) {

        this.classesService.getUnitClasses(unitId)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {
                    this.unitClasses = response.data as Class[];
                } else {
                    this.alertService.error(response.error);
                }
            }, err => this.alertService.error('Houve um erro ao buscar as turmas. Falha na comunicação com a API'));
    }

    private getTeachersLink(unit: number) {
        this.teacherService.getTeachersLink(unit)
            .subscribe( res => {
                const response = res.body as ResponseApi;

                if (!response.error) {

                    this.teachersLink = response.data as TeacherLink[];
                    this.dataSource = new MatTableDataSource(this.teachersLink);

                    this.dataSource.filterPredicate =
                       (data: TeacherLink, filter: number) => {

                           // tslint:disable-next-line:triple-equals
                           if (data.classId == filter) {
                               return true;
                           } else {
                               return false;
                           }
                       };

                } else {
                    this.alertService.error(response.error);
                    this.dataSource = null;
                    this.teachersLink = null;
                }
            }, err => this.alertService.error('Houve um erro ao buscar os vínculos. Falha na comunicação com API'));
    }

    removeTeacherLink(linkId: number) {
        if (confirm('Deseja realmente remover o vínculo?')) {
            this.teacherService.removeTeacherLink(linkId)
                .subscribe( res => {
                    const response = res.body as ResponseApi;

                    if (!response.error) {
                        this.alertService.success('Vínculo removido com sucesso', 2000);
                        this.getTeachersLink(this.userService.getUserUnit());
                    } else {
                        this.alertService.error(response.error);
                    }
                }, err => this.alertService.error('Houve um erro ao remover o vínculo falha na comunicação com a API'));
        }
    }
}
