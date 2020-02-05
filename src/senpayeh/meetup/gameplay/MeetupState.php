<?php

namespace senpayeh\meetup\gameplay;

interface MeetupState {

    const WAITING = 0;
    const STARTING = 1;
    const GRACE = 2;
    const PVP = 3;
    const END = 4;

}
