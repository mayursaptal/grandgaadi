import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { Scanner2PageRoutingModule } from './scanner2-routing.module';

import { Scanner2Page } from './scanner2.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    Scanner2PageRoutingModule
  ],
  declarations: [Scanner2Page]
})
export class Scanner2PageModule {}
