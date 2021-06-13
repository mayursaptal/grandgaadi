import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { PodPageRoutingModule } from './pod-routing.module';

import { PodPage } from './pod.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    PodPageRoutingModule
  ],
  declarations: [PodPage]
})
export class PodPageModule {}
