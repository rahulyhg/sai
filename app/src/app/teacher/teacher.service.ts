import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Injectable({providedIn: 'root'})

export class TeacherService {

    private API = environment.ApiUrl;

    constructor(private http: HttpClient) {}

    registerTeacher(name: string, email: string, unit: number) {
        return this.http.post(this.API + '/teacher/registerTeacher', {name, email, unit}, {observe: 'response'});
    }

    getTeachersUnit(unit: number) {
        return this.http.post(this.API + '/teacher/getTeachersUnit', {unit}, {observe: 'response'});
    }

    removeTeacher(teacherId: number) {
        return this.http.post(this.API + '/teacher/removeTeacher', {teacherId}, {observe: 'response'});
    }

    linkDiscipline(data) {
        return this.http.post(this.API + '/teacher/linkDiscipline', data, {observe: 'response'});
    }

    getTeachersLink(unit: number) {
        return this.http.post(this.API + '/teacher/getTeachersLink', {unit}, {observe: 'response'});
    }

    removeTeacherLink(linkId: number) {
        return this.http.post(this.API + '/teacher/removeTeacherLink', {linkId}, {observe: 'response'});
    }

}
