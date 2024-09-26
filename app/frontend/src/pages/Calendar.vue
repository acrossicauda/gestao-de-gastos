<template>
  <!-- <FlexboxLayout flexWrap="wrap">
    <div class="d-none">
      <CalendarComp2 />
    </div>
    <div>
      <DayPilotMonth id="dp" :config="config" ref="monthRef" />
    </div>
  </FlexboxLayout> -->
  <DayPilotMonth id="dp" :config="config" ref="monthRef" />
</template>

<script setup>
import {DayPilot, DayPilotMonth} from '@daypilot/daypilot-lite-vue';
import { ref, reactive, onMounted } from 'vue';
import CalendarComp2 from '../components/CalendarComp2.vue';



function getDateNow() {
  let dateToString = d => `${d.getFullYear()}-${('00' + (d.getMonth() + 1)).slice(-2)}-${('00' + d.getDate()).slice(-2)}`
  let date = new Date();

  return dateToString(date);
}


async function getEvents() {

  let events = [
      {
          id: 1,
          start: getDateNow()+"T00:00:00", //"2021-09-10T10:00:00",
          end: getDateNow()+"T00:00:00", //"2021-09-10T10:00:00",
          text: "Event Teste",
          backColor: "#6aa84f",
          borderColor: "#38761d",
      },
      {
          id: 2,
          start: "2023-02-28T13:00:00",
          end: "2025-02-28T16:00:00",
          text: "Event 2",
          // backColor: "#f1c232",
          // borderColor: "#bf9000",
      },
  ];


  return events;
}

function setCookie(cname, cvalue) {
  let d = new Date();
  d.setTime(d.getTime() + 1 * 24 * 60 * 60 * 1000);
  let expires = "expires=" + d.toUTCString();
  document.cookie =
    cname + "=" + cvalue + ";" + expires + ";path=/";
}
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

async function auth() {
  let __token = getCookie('__token');
  //setCookie('__token', __token);
  console.log(getCookie('__token'))
  return __token;
}

async function getCalendar() {
  let resp = [];
  let __token = await auth();
  const options = {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      Authorization: 'Bearer ' + __token
    }
  };

  await fetch('http://localhost:8000/api/calendar', options)
    .then(response => resp = response.json() )
    // .then(response => console.log(response))
    .catch(err => console.error(err));

    return resp;
}


async function setCalendar(args) {
  let resp = [];
  let __token = await auth();

  let body = {
    "start":args.start,
    "end":args.end,
    "text":args.text,
    "backColor":args.backColor,
    "borderColor":args.borderColor
  };

  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      Authorization: 'Bearer ' + __token
    },
    body: JSON.stringify(body)
  };

  await fetch('http://localhost:8000/api/calendar', options)
    .then(response => resp = response.json() )
    // .then(response => console.log(response))
    .catch(err => console.error(err));

  return resp;
}



const config = reactive({
  startDate: getDateNow(),
  viewType: "Week",
  durationBarVisible: true,
  eventHeight: 30,
  onEventMoved: (args) => {
    const name = args.e.data.text;
    console.log(`Event ${name} moved to ${args.newStart}`);
  },
  onEventResized: (args) => {
    console.log("Event resized: " + args.e.text());
  },
  onEventClicked: async (args) => {
    const form = [
      {name: "Text", id: "text"}
    ];
    const modal = await DayPilot.Modal.form(form, args.e.data);
    if (modal.canceled) {
      return;
    }
    console.log('TESTEEEE');
    console.log(modal.result);
    monthRef.value.control.events.update(modal.result);
  },
  onTimeRangeSelected: async args => {
    const modal = await DayPilot.Modal.prompt("Create a new event:", "Event 1");
    const dp = args.control;
    dp.clearSelection();
    if (modal.canceled) { return; }
    console.log(args);

    args.text = modal.result;
    args.backColor = "#6aa84f";
    args.borderColor = "#38761d";

    setCalendar(args).then(function (resp) {
      console.log(resp);
      if(!resp.success) {
        return false;
      }
      dp.events.add({
        start: args.start,
        end: args.end,
        id: DayPilot.guid(),
        text: modal.result
      });


      console.log(args)
      console.log(dp.events)
    });
  },
  onBeforeEventRender: args => {
    const color = colors[args.data.type] || "#3c78d8";
    args.data.backColor = color + "55";
    args.data.borderColor = color + "44";
    args.data.barColor = color + "66";

    args.data.areas = [
      {
        right: 4,
        top: 3,
        width: 24,
        height: 24,
        padding: 2,
        fontColor: "#444444",
        backColor: color + "55",
        symbol: "icons/daypilot.svg#threedots-v",
        action: "ContextMenu",
        style: "border-radius: 50%"
      }
    ];

    if (args.data.locked) {
      args.data.text += " (locked)";
      args.data.areas.push({
        right: 30,
        top: 3,
        width: 24,
        height: 24,
        padding: 4,
        fontColor: "#444444",
        backColor: color + "55",
        symbol: "icons/daypilot.svg#padlock",
        style: "border-radius: 50%",
        onClick: async args => {
          const modal = await DayPilot.Modal.confirm("Do you really want to unlock this event?");
          if (modal.canceled) {
            return;
          }
          toggleEventLock(args.source);
        }
      });

      args.data.moveDisabled = true;
      args.data.resizeDisabled = true;
      args.data.clickDisabled = true;
      args.data.deleteDisabled = true;
    }
  },
  contextMenu: new DayPilot.Menu({
    items: [
      {text: "Edit...", onClick: args => editEvent(args.source) },
      {text: "Delete", symbol: "icons/daypilot.svg#x-4", onClick: args => deleteEvent(args.source) },
      {text: "Lock", symbol: "icons/daypilot.svg#padlock", onClick: args => toggleEventLock(args.source) },
      {text: "-"},
      {text: "Duplicate", onClick: args => duplicateEvent(args.source) },
      {text: "Postpone", symbol: "icons/daypilot.svg#minichevron-right-4", onClick: args => postponeEvent(args.source) },
      {text: "-"},
      {text: "Type", items: [
          {text: "Event", icon: "icon icon-blue", onClick: args => updateEventType(args.source, "event")},
          {text: "Task", icon: "icon icon-green", onClick: args => updateEventType(args.source, "task")},
          {text: "Reminder", icon: "icon icon-yellow", onClick: args => updateEventType(args.source, "reminder")},
          {text: "Holiday", icon: "icon icon-red", onClick: args => updateEventType(args.source, "holiday")},
        ]
      }
    ],
    onShow: args => {
      const e = args.source;
      const locked = e.data.locked;

      // update the lock/unlock text
      args.menu.items[2].text = locked ? "Unlock" : "Lock";

      // disable actions for locked
      args.menu.items[0].disabled = locked;
      args.menu.items[1].disabled = locked;
      args.menu.items[5].disabled = locked;
      args.menu.items[7].disabled = locked;
    }
  })
});
const monthRef = ref(null);

const colors = {
  "event": "#3c78d8",
  "task": "#6aa84f",
  "reminder": "#f1c232",
  "holiday": "#cc0000",
};

const editEvent = async (e) => {
  const form = [
    {name: "Text", id: "text"}
  ];

  const modal = await DayPilot.Modal.form(form, e.data);
  if (modal.canceled) {
    return;
  }

  monthRef.value.control.events.update(modal.result);
};

const deleteEvent = async (e) => {
  const modal = await DayPilot.Modal.confirm("Do you really want to delete this event?");
  if (modal.canceled) {
    return;
  }
  monthRef.value.control.events.remove(e);
};

const duplicateEvent = (e) => {
  const newEvent = {
    ...e.data,
    id: DayPilot.guid()
  };
  monthRef.value.control.events.add(newEvent);
};

const postponeEvent = (e) => {
  e.data.start = e.start().addDays(1);
  e.data.end = e.end().addDays(1);
  monthRef.value.control.events.update(e);
};

const updateEventType = (e, type) => {
  e.data.type = type;
  monthRef.value.control.events.update(e);
}

const toggleEventLock = (e) => {
  e.data.locked = !e.data.locked;
  monthRef.value.control.events.update(e);
};


const loadEvents = async () => {
  let events;
  let response = await getCalendar();

  if(!response.success) {
    throw "ERRO";
  }
  events = response.data;

  config.events = events;

  // console.log(monthRef.value.control.events)
  //const modal = await DayPilot.Modal.form(form, args.e.data);
  events.forEach(event => {
    monthRef.value.control.events.add(event);
  })

};

onMounted(() => {
  loadEvents();
});


</script>


<style>

.month_default_event_inner {
  border-radius: 20px;
  padding-left: 35px;

  white-space: nowrap;
}

.month_default_event_bar_inner {
  width: 30px;
  border-radius: 20px;
}

.month_default_event svg:hover {
  color: #000000;
  cursor: pointer;
}

/* context menu icons */
.icon:before {
  position: absolute;
  left: 0px;
  margin-left: 8px;
  margin-top: 3px;
  width: 14px;
  height: 14px;
  content: '';
}

.icon-blue:before { background-color: #3c78d8; }
.icon-green:before { background-color: #6aa84f; }
.icon-yellow:before { background-color: #f1c232; }
.icon-red:before { background-color: #cc0000; }

</style>
