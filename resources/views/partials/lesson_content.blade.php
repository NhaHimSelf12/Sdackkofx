<style>
    .lesson-grid-main { display: grid; gap: 24px; align-items: start; }
    @media (min-width: 1024px) {
        .lesson-grid-main { grid-template-columns: 280px 1fr; }
    }
</style>
<div class="lesson-grid-main">
    <!-- Sidebar for Lessons -->
    <div class="card sidebar-nav" style="padding: 0; position: sticky; top: 100px; background: var(--surface); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        <div style="padding: 20px; border-bottom: 1px solid var(--border); font-weight: 800; font-size: 18px; color: var(--text); background: linear-gradient(135deg, var(--blue-soft), transparent);">
            📚 មេរៀន Forex 
        </div>
        <div style="display: flex; flex-direction: column; padding: 12px; gap: 6px;">
            <a href="#part1" class="lesson-nav-item">ផ្នែកទី 1: Forex Fundamentals</a>
            <a href="#part2" class="lesson-nav-item">ផ្នែកទី 2: Leverage & Orders</a>
            <a href="#part3" class="lesson-nav-item">ផ្នែកទី 3: Market Structure</a>
            <a href="#part4" class="lesson-nav-item">ផ្នែកទី 4: Candlestick</a>
            <a href="#part5" class="lesson-nav-item">ផ្នែកទី 5: Technical Analysis</a>
            <a href="#part6" class="lesson-nav-item">ផ្នែកទី 6: Fundamental Analysis</a>
            <a href="#part7" class="lesson-nav-item">ផ្នែកទី 7: Risk Management</a>
            <a href="#part8" class="lesson-nav-item">ផ្នែកទី 8: Strategy & Psychology</a>
            <a href="#roadmap" class="lesson-nav-item highlight-nav">📅 ផែនការសិក្សា 8 សប្ដាហ៍</a>
        </div>
    </div>

    <!-- Lesson Content -->
    <div class="card main-lesson-content" style="padding: 40px; background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
        
        <div class="lesson-header" style="text-align: center; margin-bottom: 40px;">
            <h1 style="font-size: 36px; color: var(--text); margin: 0 0 16px; font-weight: 900; line-height: 1.4; background: -webkit-linear-gradient(45deg, var(--blue), #9b51e0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">មេរៀន និង Concept សំខាន់ៗអំពី Forex</h1>
            <p style="font-size: 18px; color: var(--text-2); max-width: 700px; margin: 0 auto; line-height: 1.6;">
                Forex គឺជាទីផ្សារប្តូររូបិយប័ណ្ណសកល ដែលដំណើរការជាទីផ្សារ decentralized/OTC ហើយរូបិយប័ណ្ណត្រូវបានជួញដូរជាគូ។
            </p>
        </div>

        <div class="alert-warning" style="background: var(--orange-soft); border-left: 4px solid var(--orange); padding: 16px 20px; border-radius: 8px; margin-bottom: 40px; color: var(--text);">
            <strong style="color: var(--orange);">⚠️ សម្គាល់៖</strong> Forex មានហានិភ័យខ្ពស់ ជាពិសេសពេលប្រើ Leverage។ ខ្លឹមសារនេះសម្រាប់ការអប់រំ មិនមែនជាការណែនាំឱ្យវិនិយោគទេ។
        </div>

        <div class="content-sections" style="display: flex; flex-direction: column; gap: 40px; font-size: 16px; color: var(--text-2); line-height: 1.8;">
            
            <!-- Part 1 -->
            <section id="part1" class="lesson-section">
                <h2 style="font-size: 24px; color: var(--blue); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid var(--border); padding-bottom: 10px;">ផ្នែកទី 1 — Forex Fundamentals</h2>
                <div class="term-grid">
                    <div class="term-item"><strong>Forex Market</strong> — ទីផ្សារទិញ និងលក់រូបិយប័ណ្ណ។</div>
                    <div class="term-item"><strong>Currency Pair</strong> — ឧទាហរណ៍ EUR/USD, GBP/USD និង USD/JPY។</div>
                    <div class="term-item"><strong>Base Currency</strong> — រូបិយប័ណ្ណខាងមុខ ដូចជា EUR ក្នុង EUR/USD។</div>
                    <div class="term-item"><strong>Quote Currency</strong> — រូបិយប័ណ្ណខាងក្រោយ ដូចជា USD ក្នុង EUR/USD។</div>
                    <div class="term-item"><strong>Exchange Rate</strong> — តម្លៃរូបិយប័ណ្ណមួយ ប្រៀបធៀបនឹងមួយទៀត។</div>
                    <div class="term-item"><strong>Major Pairs</strong> — គូរូបិយប័ណ្ណដែលមាន USD និងមានការជួញដូរច្រើន។</div>
                    <div class="term-item"><strong>Minor/Cross Pairs</strong> — គូរូបិយប័ណ្ណធំៗដែលមិនមាន USD។</div>
                    <div class="term-item"><strong>Exotic Pairs</strong> — រូបិយប័ណ្ណប្រទេសធំមួយ ជាមួយរូបិយប័ណ្ណទីផ្សារកំពុងអភិវឌ្ឍ។</div>
                    <div class="term-item"><strong>Long/Buy</strong> — ទិញដោយរំពឹងថាតម្លៃនឹងឡើង។</div>
                    <div class="term-item"><strong>Short/Sell</strong> — លក់ដោយរំពឹងថាតម្លៃនឹងចុះ។</div>
                    <div class="term-item"><strong>Bid និង Ask</strong> — Bid ជាតម្លៃដែលអាចលក់; Ask ជាតម្លៃដែលអាចទិញ។</div>
                    <div class="term-item"><strong>Spread</strong> — ភាពខុសគ្នារវាង Ask និង Bid។</div>
                    <div class="term-item"><strong>Pip</strong> — ឯកតាវាស់ចលនាតម្លៃ; ជាទូទៅ 0.0001 ខណៈគូ JPY ជាទូទៅប្រើ 0.01។</div>
                    <div class="term-item"><strong>Pipette</strong> — 1/10 នៃ Pip។</div>
                    <div class="term-item"><strong>Lot Size</strong> — Standard lot = 100,000 units, Mini = 10,000 និង Micro = 1,000 units។</div>
                    <div class="term-item"><strong>Volatility</strong> — កម្រិតដែលតម្លៃឡើងចុះលឿន ឬខ្លាំង។</div>
                    <div class="term-item"><strong>Liquidity</strong> — ភាពងាយស្រួលក្នុងការទិញ ឬលក់ដោយមិនធ្វើឱ្យតម្លៃប្រែប្រួលខ្លាំង។</div>
                    <div class="term-item"><strong>Slippage</strong> — តម្លៃដែល Order បាន Execute ខុសពីតម្លៃដែលបានស្នើ។</div>
                </div>
            </section>

            <!-- Part 2 -->
            <section id="part2" class="lesson-section">
                <h2 style="font-size: 24px; color: var(--blue); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid var(--border); padding-bottom: 10px;">ផ្នែកទី 2 — Leverage, Margin និង Orders</h2>
                <div class="term-grid">
                    <div class="term-item"><strong>Leverage</strong> — អនុញ្ញាតឱ្យគ្រប់គ្រង Position ធំជាងប្រាក់ដែលមាន។</div>
                    <div class="term-item"><strong>Margin</strong> — ប្រាក់ដែល Broker ទុកជាបញ្ចាំសម្រាប់បើក Position។</div>
                    <div class="term-item"><strong>Free Margin</strong> — ទឹកប្រាក់នៅសល់ដែលអាចប្រើបើក Position ថ្មី។</div>
                    <div class="term-item"><strong>Margin Level</strong> — ភាគរយបង្ហាញស្ថានភាព Margin របស់ Account។</div>
                    <div class="term-item"><strong>Margin Call</strong> — ការព្រមានថា Equity ធ្លាក់ចុះខ្លាំង។</div>
                    <div class="term-item"><strong>Stop Out</strong> — Broker បិទ Position ដោយស្វ័យប្រវត្តិ ព្រោះ Margin មិនគ្រប់។</div>
                    <div class="term-item"><strong>Market Order</strong> — ទិញ ឬលក់ភ្លាមៗតាមតម្លៃទីផ្សារ។</div>
                    <div class="term-item"><strong>Limit Order</strong> — រង់ចាំចូលនៅតម្លៃប្រសើរជាងតម្លៃបច្ចុប្បន្ន។</div>
                    <div class="term-item"><strong>Stop Order</strong> — រង់ចាំចូលក្រោយតម្លៃឆ្លងកាត់កម្រិតជាក់លាក់។</div>
                    <div class="term-item"><strong>Stop Loss</strong> — កំណត់ចំណុចបិទ Position ដើម្បីគ្រប់គ្រងការខាត។</div>
                    <div class="term-item"><strong>Take Profit</strong> — កំណត់ចំណុចយកប្រាក់ចំណេញ។</div>
                    <div class="term-item"><strong>Trailing Stop</strong> — Stop Loss ដែលផ្លាស់ទីតាមទិសចំណេញ។</div>
                </div>
                <div style="background: var(--red-soft); color: var(--red); padding: 16px; border-radius: 8px; margin-top: 16px; font-size: 14px;">
                    <strong>ចំណាំ៖</strong> Leverage អាចពង្រីកទាំងចំណេញ និងការខាត។ CFTC ព្រមានថា Trader អាចបាត់បង់ Margin ទាំងអស់ ហើយក្នុងករណីខ្លះអាចលើសពីចំនួននោះ។
                </div>
            </section>

            <!-- Part 3 -->
            <section id="part3" class="lesson-section">
                <h2 style="font-size: 24px; color: var(--blue); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid var(--border); padding-bottom: 10px;">ផ្នែកទី 3 — Market Structure និង Price Action</h2>
                
                <!-- Chart Anatomy Image -->
                <div style="margin-bottom: 24px; text-align: center; background: var(--surface); padding: 16px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <img src="{{ asset('images/chart-anatomy.png') }}" alt="Chart Anatomy" style="max-width: 100%; height: auto; border-radius: 8px;">
                    <p style="font-size: 14px; color: var(--text-3); margin-top: 12px; margin-bottom: 0;">(ប្រសិនបើរូបភាពមិនចេញ សូម Copy រូបភាព Chart ដាក់ចូលក្នុង Folder <code style="background: var(--raised); padding: 2px 6px; border-radius: 4px;">public/images/chart-anatomy.png</code>)</p>
                </div>

                <div class="term-grid">
                    <div class="term-item"><strong>Uptrend</strong> — Higher High និង Higher Low។</div>
                    <div class="term-item"><strong>Downtrend</strong> — Lower High និង Lower Low។</div>
                    <div class="term-item"><strong>Sideway/Range</strong> — តម្លៃផ្លាស់ទីរវាង Support និង Resistance។</div>
                    <div class="term-item"><strong>Support</strong> — តំបន់ដែលអាចមានកម្លាំងទិញចូល។</div>
                    <div class="term-item"><strong>Resistance</strong> — តំបន់ដែលអាចមានកម្លាំងលក់ចូល។</div>
                    <div class="term-item"><strong>Breakout</strong> — តម្លៃឆ្លងផុត Support ឬ Resistance។</div>
                    <div class="term-item"><strong>False Breakout</strong> — តម្លៃឆ្លងផុតបន្តិច បន្ទាប់មកត្រឡប់ចូលវិញ។</div>
                    <div class="term-item"><strong>Retest</strong> — តម្លៃត្រឡប់មកសាកល្បងតំបន់ដែលទើប Breakout។</div>
                    <div class="term-item"><strong>Pullback</strong> — ការត្រឡប់តម្លៃបណ្តោះអាសន្នក្នុង Trend។</div>
                    <div class="term-item"><strong>Reversal</strong> — ការប្រែទិស Trend។</div>
                    <div class="term-item"><strong>Swing High/Swing Low</strong> — កំពូល និងបាតសំខាន់ៗនៅលើ Chart។</div>
                    <div class="term-item"><strong>Supply and Demand</strong> — តំបន់ដែលសម្ពាធទិញ ឬលក់មិនស្មើគ្នា។</div>
                    <div class="term-item"><strong>Liquidity Sweep</strong> — តម្លៃទៅយក Orders ជុំវិញ High/Low មុនផ្លាស់ទីទៅទិសផ្សេង។</div>
                    <div class="term-item"><strong>Market Structure Shift</strong> — សញ្ញាដំបូងដែល Structure ចាស់អាចកំពុងប្តូរ។</div>
                </div>
                <div style="background: var(--blue-soft); color: var(--blue); padding: 16px; border-radius: 8px; margin-top: 16px; font-size: 14px;">
                    <strong>គន្លឹះ៖</strong> Support និង Resistance គួរត្រូវបានមើលជា "តំបន់" មិនមែនជាបន្ទាត់ដែលតម្លៃមិនអាចឆ្លងបានទេ។
                </div>
            </section>

            <!-- Part 4 -->
            <section id="part4" class="lesson-section">
                <h2 style="font-size: 24px; color: var(--blue); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid var(--border); padding-bottom: 10px;">ផ្នែកទី 4 — Candlestick</h2>
                <div class="term-grid">
                    <div class="term-item"><strong>Open, High, Low, Close (OHLC)</strong> — ព័ត៌មានសំខាន់ក្នុង Candlestick មួយ។</div>
                    <div class="term-item"><strong>Body</strong> — ចម្ងាយរវាងតម្លៃ Open និង Close។</div>
                    <div class="term-item"><strong>Wick/Shadow</strong> — បង្ហាញតម្លៃខ្ពស់ និងទាបដែលបានទៅដល់។</div>
                    <div class="term-item"><strong>Doji</strong> — បង្ហាញភាពស្ទាក់ស្ទើររវាងអ្នកទិញ និងអ្នកលក់។</div>
                    <div class="term-item"><strong>Pin Bar</strong> — Candle មាន Wick វែង ដែលអាចបង្ហាញ Price Rejection។</div>
                    <div class="term-item"><strong>Engulfing Pattern</strong> — Candle ថ្មីគ្របដណ្តប់ Body របស់ Candle មុន។</div>
                    <div class="term-item"><strong>Inside Bar</strong> — Candle ថ្មីស្ថិតក្នុង High-Low របស់ Candle មុន។</div>
                    <div class="term-item"><strong>Morning Star/Evening Star</strong> — Pattern ច្រើន Candle ដែលអាចបង្ហាញ Reversal។</div>
                    <div class="term-item"><strong>Three White Soldiers/Three Black Crows</strong> — Pattern បង្ហាញ Momentum ខ្លាំង។</div>
                </div>
                <div style="background: var(--green-soft); color: var(--green); padding: 16px; border-radius: 8px; margin-top: 16px; font-size: 14px;">
                    <strong>ការអនុវត្ត៖</strong> Candlestick Pattern មិនគួរប្រើតែឯងទេ។ វាមានទម្ងន់ច្រើនជាង ពេលកើតនៅ Support/Resistance ហើយមាន Confirmation បន្ថែម។
                </div>
            </section>

            <!-- Part 5 -->
            <section id="part5" class="lesson-section">
                <h2 style="font-size: 24px; color: var(--blue); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid var(--border); padding-bottom: 10px;">ផ្នែកទី 5 — Technical Analysis</h2>
                <div class="term-grid">
                    <div class="term-item"><strong>Moving Average</strong> — បង្ហាញតម្លៃមធ្យម និងទិស Trend។</div>
                    <div class="term-item"><strong>EMA/SMA</strong> — EMA ឆ្លើយតបទៅនឹងតម្លៃថ្មីលឿនជាង SMA។</div>
                    <div class="term-item"><strong>RSI</strong> — Indicator សម្រាប់វាស់ Momentum និង Overbought/Oversold។</div>
                    <div class="term-item"><strong>MACD</strong> — សម្រាប់មើល Momentum និងការផ្លាស់ប្តូរ Trend។</div>
                    <div class="term-item"><strong>ATR</strong> — វាស់កម្រិត Volatility មិនមែនទិសតម្លៃ។</div>
                    <div class="term-item"><strong>Bollinger Bands</strong> — បង្ហាញ Volatility និងការពង្រីក/រួមតូចរបស់តម្លៃ។</div>
                    <div class="term-item"><strong>Fibonacci Retracement</strong> — តំបន់ដែល Trader ខ្លះប្រើសម្រាប់ស្វែងរក Pullback។</div>
                    <div class="term-item"><strong>Trendline និង Channel</strong> — ជួយមើលទិស និងដែនចលនាតម្លៃ។</div>
                    <div class="term-item"><strong>Chart Patterns</strong> — Triangle, Flag, Wedge, Double Top/Bottom និង Head & Shoulders។</div>
                    <div class="term-item"><strong>Multi-Timeframe Analysis</strong> — មើល Timeframe ធំសម្រាប់ទិស ហើយ Timeframe តូចសម្រាប់ Entry។</div>
                </div>
                <p style="margin-top: 16px; font-style: italic; background: rgba(0,0,0,0.03); padding: 12px; border-radius: 8px;">💡 Indicator គឺជាឧបករណ៍ជំនួយ មិនមែនជាម៉ាស៊ីនទាយទីផ្សារទេ។</p>
            </section>

            <!-- Part 6 -->
            <section id="part6" class="lesson-section">
                <h2 style="font-size: 24px; color: var(--blue); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid var(--border); padding-bottom: 10px;">ផ្នែកទី 6 — Fundamental Analysis</h2>
                <div class="term-grid">
                    <div class="term-item"><strong>Interest Rate</strong> — អត្រាការប្រាក់របស់ធនាគារកណ្ដាលអាចប៉ះពាល់ដល់តម្លៃរូបិយប័ណ្ណ។</div>
                    <div class="term-item"><strong>Inflation/CPI</strong> — ទិន្នន័យថ្លៃទំនិញ និងសេវាកម្ម។</div>
                    <div class="term-item"><strong>GDP</strong> — វាស់ទំហំ និងកំណើនសេដ្ឋកិច្ច។</div>
                    <div class="term-item"><strong>Employment/NFP</strong> — ទិន្នន័យការងារដែលអាចធ្វើឱ្យ USD ប្រែប្រួលខ្លាំង។</div>
                    <div class="term-item"><strong>Central Bank</strong> — Fed, ECB, BoE, BoJ និងធនាគារកណ្ដាលផ្សេងៗ។</div>
                    <div class="term-item"><strong>Monetary Policy</strong> — Hawkish អាចគាំទ្ររូបិយប័ណ្ណ; Dovish អាចដាក់សម្ពាធ ប៉ុន្តែមិនមែនគ្រប់ករណី។</div>
                    <div class="term-item"><strong>Economic Calendar</strong> — ប្រើសម្រាប់ដឹងថ្ងៃ និងម៉ោងចេញព័ត៌មានសំខាន់។</div>
                    <div class="term-item"><strong>Risk-on/Risk-off Sentiment</strong> — អារម្មណ៍ទីផ្សារចំពោះហានិភ័យ។</div>
                    <div class="term-item"><strong>Geopolitical Events</strong> — សង្គ្រាម ការបោះឆ្នោត និងវិបត្តិអាចបង្កើន Volatility។</div>
                    <div class="term-item"><strong>Correlation</strong> — គូរូបិយប័ណ្ណ ឬទ្រព្យសកម្មមួយចំនួនអាចផ្លាស់ទីស្រដៀង ឬផ្ទុយគ្នា។</div>
                </div>
                <p style="margin-top: 16px; font-style: italic; background: rgba(0,0,0,0.03); padding: 12px; border-radius: 8px;">💡 Fundamental Analysis ពិនិត្យទិន្នន័យសេដ្ឋកិច្ច នយោបាយ គោលនយោបាយការប្រាក់ និងកត្តាផ្សេងៗដែលអាចប៉ះពាល់ដល់រូបិយប័ណ្ណ។</p>
            </section>

            <!-- Part 7 -->
            <section id="part7" class="lesson-section">
                <h2 style="font-size: 24px; color: var(--blue); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid var(--border); padding-bottom: 10px;">ផ្នែកទី 7 — Risk Management</h2>
                <div class="term-grid">
                    <div class="term-item"><strong>Risk per Trade</strong> — អ្នកចាប់ផ្ដើមអាចហ្វឹកហាត់ជាមួយ Risk តូច ដូចជា 0.5%–1% ក្នុងមួយ Trade។</div>
                    <div class="term-item"><strong>Position Sizing</strong> — គណនា Lot តាមទំហំ Stop Loss និងប្រាក់ដែលអាចទទួលការខាតបាន។</div>
                    <div class="term-item"><strong>Risk-to-Reward Ratio</strong> — ឧទាហរណ៍ Risk $10 ដើម្បី Target $20 គឺ 1:2។</div>
                    <div class="term-item"><strong>Maximum Daily Loss</strong> — កំណត់ការខាតអតិបរមាប្រចាំថ្ងៃ។</div>
                    <div class="term-item"><strong>Drawdown</strong> — ភាគរយដែល Account ធ្លាក់ពីកម្រិតខ្ពស់បំផុត។</div>
                    <div class="term-item"><strong>Correlation Risk</strong> — បើក Trades ស្រដៀងគ្នាច្រើន អាចមានន័យថាកំពុង Risk លើគំនិតតែមួយ។</div>
                    <div class="term-item"><strong>News Risk</strong> — Spread និង Slippage អាចកើនឡើងពេលព័ត៌មានធំ។</div>
                    <div class="term-item"><strong>Risk of Ruin</strong> — ហានិភ័យដែលការខាតបន្តបន្ទាប់ធ្វើឱ្យ Account មិនអាចស្ដារឡើងវិញ។</div>
                </div>
                
                <div style="background: var(--raised); padding: 24px; border-radius: 12px; margin-top: 24px; border-left: 4px solid var(--blue);">
                    <strong style="color: var(--text); font-size: 18px; display: block; margin-bottom: 12px;">🧮 រូបមន្តសាមញ្ញ៖</strong>
                    <code style="display: block; background: rgba(0,0,0,0.1); padding: 12px; border-radius: 6px; margin-bottom: 12px; font-family: monospace; font-size: 16px;">ប្រាក់អាចខាត = Account Balance × Risk %</code>
                    <p style="margin: 0;"><strong>ឧទាហរណ៍៖</strong> Account $1,000 និង Risk 1% → អាចខាតអតិបរមា $10 ក្នុងមួយ Trade។</p>
                </div>
            </section>

            <!-- Part 8 -->
            <section id="part8" class="lesson-section">
                <h2 style="font-size: 24px; color: var(--blue); margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid var(--border); padding-bottom: 10px;">ផ្នែកទី 8 — Strategy និង Psychology</h2>
                <div class="term-grid">
                    <div class="term-item"><strong>Trading Plan</strong> — កំណត់ Pair, Session, Setup, Entry, Stop Loss, Take Profit និង Risk។</div>
                    <div class="term-item"><strong>Trading Journal</strong> — កត់ត្រារូប Chart មូលហេតុចូល លទ្ធផល និងអារម្មណ៍។</div>
                    <div class="term-item"><strong>Backtesting</strong> — សាកល្បង Rule លើទិន្នន័យចាស់។</div>
                    <div class="term-item"><strong>Forward Testing</strong> — សាកល្បងលើ Demo ក្នុងទីផ្សារបច្ចុប្បន្ន។</div>
                    <div class="term-item"><strong>Trading Edge</strong> — Rule ដែលមានលទ្ធផលវិជ្ជមានក្រោយ Sample Size គ្រប់គ្រាន់។</div>
                    <div class="term-item"><strong>Win Rate</strong> — ភាគរយ Trade ដែលឈ្នះ។</div>
                    <div class="term-item"><strong>Expectancy</strong> — ចំណេញ ឬការខាតមធ្យមដែលរំពឹងក្នុងមួយ Trade។</div>
                    <div class="term-item"><strong>FOMO</strong> — ខ្លាចបាត់ឱកាស ហើយចូល Trade ដោយគ្មាន Setup។</div>
                    <div class="term-item"><strong>Revenge Trading</strong> — ចូល Trade ដើម្បីយកប្រាក់ដែលទើបខាតត្រឡប់មកវិញ។</div>
                    <div class="term-item"><strong>Overtrading</strong> — ចូល Trade ច្រើនហួស Trading Plan។</div>
                    <div class="term-item"><strong>Discipline</strong> — អនុវត្តតាម Rule ទោះបីជាមានអារម្មណ៍ចង់បំពានក៏ដោយ។</div>
                    <div class="term-item"><strong>Patience</strong> — រង់ចាំ Setup ដែលស្របតាម Plan។</div>
                </div>
                <div style="text-align: center; margin-top: 32px;">
                    <strong style="color: var(--blue); font-size: 18px; padding: 16px 32px; background: var(--blue-soft); border-radius: 30px; display: inline-block; box-shadow: 0 4px 12px rgba(59,130,246,0.2);">"Process over Profit" — វាយតម្លៃគុណភាពការសម្រេចចិត្ត មិនមែនតែលទ្ធផល Trade មួយ។</strong>
                </div>
            </section>

            <!-- Roadmap -->
            <section id="roadmap" class="lesson-section" style="background: linear-gradient(135deg, var(--surface) 0%, var(--blue-soft) 100%); padding: 40px; border-radius: 16px; margin-top: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="font-size: 32px; color: var(--text); margin-bottom: 10px; font-weight: 900;">ផែនការសិក្សា 8 សប្ដាហ៍</h2>
                    <p style="color: var(--text-2); font-size: 16px;">រៀបចំការសិក្សារបស់អ្នកមួយជំហានម្ដងៗ ដើម្បីភាពជោគជ័យក្នុងការជួញដូរ</p>
                </div>

                <!-- Roadmap Image -->
                <div style="margin-bottom: 40px; text-align: center; background: rgba(255,255,255,0.5); padding: 16px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <img src="{{ asset('images/roadmap.png') }}" alt="Learning Roadmap" style="max-width: 100%; height: auto; border-radius: 8px;">
                    <p style="font-size: 14px; color: var(--text-3); margin-top: 12px; margin-bottom: 0;">(ប្រសិនបើរូបភាពមិនចេញ សូម Copy រូបភាព Roadmap ដាក់ចូលក្នុង Folder <code style="background: var(--raised); padding: 2px 6px; border-radius: 4px;">public/images/roadmap.png</code>)</p>
                </div>
                
                <div class="roadmap-timeline">
                    <div class="roadmap-item">
                        <div class="roadmap-week">សប្ដាហ៍ 1</div>
                        <div class="roadmap-content">Currency pairs, Pip, Lot, Spread, Bid/Ask។</div>
                    </div>
                    <div class="roadmap-item">
                        <div class="roadmap-week">សប្ដាហ៍ 2</div>
                        <div class="roadmap-content">Candlestick, Trend និង Market Structure។</div>
                    </div>
                    <div class="roadmap-item">
                        <div class="roadmap-week">សប្ដាហ៍ 3</div>
                        <div class="roadmap-content">Support, Resistance, Breakout និង Retest។</div>
                    </div>
                    <div class="roadmap-item">
                        <div class="roadmap-week">សប្ដាហ៍ 4</div>
                        <div class="roadmap-content">Technical Indicators និង Multi-Timeframe។</div>
                    </div>
                    <div class="roadmap-item">
                        <div class="roadmap-week">សប្ដាហ៍ 5</div>
                        <div class="roadmap-content">Fundamental Analysis និង Economic Calendar។</div>
                    </div>
                    <div class="roadmap-item">
                        <div class="roadmap-week">សប្ដាហ៍ 6</div>
                        <div class="roadmap-content">Stop Loss, Position Size និង Risk-to-Reward។</div>
                    </div>
                    <div class="roadmap-item">
                        <div class="roadmap-week">សប្ដាហ៍ 7</div>
                        <div class="roadmap-content">បង្កើត Strategy មួយ ហើយ Backtest យ៉ាងហោចណាស់ 100 Trades។</div>
                    </div>
                    <div class="roadmap-item">
                        <div class="roadmap-week" style="background: var(--green); color: white; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);">សប្ដាហ៍ 8</div>
                        <div class="roadmap-content" style="border-color: var(--green); background: rgba(34, 197, 94, 0.05); color: var(--green); font-weight: 700;">Forward Test លើ Demo និងរក្សា Trading Journal។</div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<style>
    .lesson-nav-item {
        padding: 12px 16px;
        color: var(--text-2);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .lesson-nav-item:hover {
        background: var(--raised);
        color: var(--blue);
        transform: translateX(4px);
    }
    .highlight-nav {
        background: var(--blue-soft);
        color: var(--blue);
        font-weight: 700;
        margin-top: 12px;
    }
    .highlight-nav:hover {
        background: rgba(59, 130, 246, 0.2);
    }
    
    .lesson-section {
        scroll-margin-top: 120px;
    }
    
    .term-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }
    
    .term-item {
        background: var(--raised);
        padding: 20px;
        border-radius: 12px;
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .term-item:hover {
        border-left-color: var(--blue);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        background: var(--surface);
    }
    .term-item strong {
        color: var(--text);
        display: block;
        margin-bottom: 8px;
        font-size: 16px;
    }
    
    /* Roadmap Timeline Styling */
    .roadmap-timeline {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: relative;
    }
    .roadmap-timeline::before {
        content: '';
        position: absolute;
        left: 50px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border);
        z-index: 0;
    }
    .roadmap-item {
        display: flex;
        align-items: center;
        gap: 24px;
        position: relative;
        z-index: 1;
    }
    .roadmap-week {
        min-width: 100px;
        padding: 10px 16px;
        background: var(--blue);
        color: white;
        font-weight: 700;
        border-radius: 30px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }
    .roadmap-content {
        flex: 1;
        background: var(--surface);
        padding: 18px 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        font-weight: 500;
        font-size: 16px;
        color: var(--text);
    }
    
    html {
        scroll-behavior: smooth;
    }

    @media (max-width: 900px) {
        .grid-main { grid-template-columns: 1fr !important; }
        .sidebar-nav { position: relative !important; top: 0 !important; margin-bottom: 24px; }
        .roadmap-timeline::before { left: 40px; }
        .roadmap-item { flex-direction: column; align-items: flex-start; gap: 12px; }
        .roadmap-week { margin-left: 0; }
        .roadmap-content { width: 100%; margin-left: 0; }
    }
</style>
