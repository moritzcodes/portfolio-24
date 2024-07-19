const birthDate = new Date('2004-12-16T00:00:00Z');

        const millisecondsInASecond = 1000;
        const millisecondsInAMinute = millisecondsInASecond * 60;
        const millisecondsInAnHour = millisecondsInAMinute * 60;
        const millisecondsInADay = millisecondsInAnHour * 24;
        const millisecondsInAYear = millisecondsInADay * 365.25;

        function updateCounter() {
            const currentDate = new Date();

            const diffMilliseconds = currentDate - birthDate;

            const totalDecimalYears = diffMilliseconds / millisecondsInAYear;

            const displayYears = totalDecimalYears.toFixed(7);
            const displayYearsOdd = Math.floor(totalDecimalYears);

            document.getElementById('counter').innerText = `${displayYears} years`;
            document.getElementById('counter-odd').innerText = `${displayYearsOdd} years old`;
        }

        updateCounter();

        setInterval(updateCounter, 1000);