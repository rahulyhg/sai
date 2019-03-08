import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Injectable({providedIn: 'root'})

export class StudentService {

    private API = environment.ApiUrl;

    constructor(private http: HttpClient) {}

    public getStudent(studentId) {
        return this.http.post(this.API + '/student/getStudent', {studentId}, {observe: 'response'});
    }

    public removeStudent(studentId) {
        return this.http.post(this.API + '/student/removeStudent', {studentId}, {observe: 'response'});
    }
}
