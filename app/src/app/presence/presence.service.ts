import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Injectable({providedIn: 'root'})

export class PresenceService {

    private API = environment.ApiUrl;
    constructor(private http: HttpClient) {}

    setStatusPresenceStudent(studentId: number, status: string) {
        return this.http.post(this.API + '/presence/setStatusPresenceStudent', {studentId, status}, {observe: 'response'});
    }

    setStatusPresenceWithCard(registerNumber: number) {
        return this.http.post(this.API + '/presence/setStatusPresenceWithCard', {registerNumber}, {observe: 'response'});
    }

    getPresences(classId: number) {
        return this.http.post(this.API + '/presence/getPresences', {classId}, {observe: 'response'});
    }
}

