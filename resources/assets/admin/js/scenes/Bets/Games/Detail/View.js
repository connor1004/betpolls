import React, { Component } from 'react';
import {
  Card, Form, FormLayout, InlineError, Button,
  TextField,
  Stack,
  Thumbnail,
  TextStyle,
  Checkbox
} from '@shopify/polaris';
import { Formik } from 'formik';
import * as Yup from 'yup';
import DatePicker from 'react-datepicker';
import Select from 'react-select';
import moment from 'moment-timezone';

import { GAME_STATUS_OPTIONS } from '../../../../configs/options';
import OptionsHelper from '../../../../helpers/OptionsHelper';
import {
  GAME_STATUS
} from '../../../../configs/enums';


class View extends Component {
  constructor(props) {
    super(props);
    this.formikRef = React.createRef();
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  componentWillReceiveProps(props) {
    if (this.props.data !== props.data) {
      this.formikRef.current.setValues(this.paramsToFormValues(props.data));
    }
  }

  formValuesToParams(values) {
    var game_info = this.props.data.game_info ? this.props.data.game_info : {};
    if (!game_info.hometeam) {
      game_info.hometeam = {};
    }
    if (!game_info.awayteam) {
      game_info.awayteam = {};
    }
    game_info.hometeam.score = values.home_team_subscore.split(',').map(val => val.trim());
    game_info.awayteam.score = values.away_team_subscore.split(',').map(val => val.trim());

    var start_at = moment(values.start_at).format('YYYY-MM-DD HH:mm:ss');
    start_at = moment.tz(start_at, "America/New_York");
    
    return {
      start_at: moment.utc(start_at).format('YYYY-MM-DD HH:mm:ss'),
      home_team_score: values.home_team_score,
      away_team_score: values.away_team_score,
      status: values.status.value,
      game_info,
      meta: values.meta,
      meta_es: values.meta_es,
      stop_voting: values.stop_voting ? 1 : 0,
      setting_manually: values.setting_manually ? 1 : 0,
      is_nulled: values.is_nulled ? 1 : 0
    };
  }

  paramsToFormValues(params) {
    var isExist = params.game_info && params.game_info.hometeam && params.game_info.hometeam.score;
    var home_team_subscore = isExist ? params.game_info.hometeam.score.join(', ') : '';
    isExist = params.game_info && params.game_info.awayteam && params.game_info.awayteam.score;
    var away_team_subscore = isExist ? params.game_info.awayteam.score.join(', ') : '';
    var start_at = moment(moment.utc(params.start_at).toDate()).tz("America/New_York").format('YYYY-MM-DD HH:mm:ss');
    return {
      start_at: moment(start_at).toDate(),
      home_team_score: params.home_team_score,
      away_team_score: params.away_team_score,
      home_team_subscore,
      away_team_subscore,
      status: OptionsHelper.getOption(params.status, GAME_STATUS_OPTIONS),
      meta: params.meta || {
        home_team: {
          game_detail: '',
          in: '',
          out: '',
          questionable: '',
          betting_tips: ''
        },
        away_team: {
          game_detail: '',
          in: '',
          out: '',
          questionable: '',
          betting_tips: ''
        }
      },
      meta_es: params.meta_es || {
        home_team: {
          game_detail: '',
          in: '',
          out: '',
          questionable: '',
          betting_tips: ''
        },
        away_team: {
          game_detail: '',
          in: '',
          out: '',
          questionable: '',
          betting_tips: ''
        }
      },
      stop_voting: params.stop_voting || false,
      setting_manually: params.setting_manually || false,
      is_nulled: params.is_nulled || false
    };
  }

  async handleSubmit(values, bags) {
    const {
      onUpdate
    } = this.props;
    await onUpdate(this.formValuesToParams(values), bags, this);
  }

  render() {
    const {
      data
    } = this.props;
    const homeTeamFirst = data.between_players || (data.league && data.league.sport_category && data.league.sport_category.home_team_first === 1);
    let first;
    let second;
    if (homeTeamFirst) {
      first = {
        label: data.between_players ? 'Player one' : 'Home team',
        score_field: 'home_team_score',
        subscore_field: 'home_team_subscore',
        team_field: 'home_team'
      };
      second = {
        label: data.between_players ? 'Player two' : 'Away team',
        score_field: 'away_team_score',
        subscore_field: 'away_team_subscore',
        team_field: 'away_team'
      };
    } else {
      first = {
        label: 'Away team',
        score_field: 'away_team_score',
        subscore_field: 'away_team_subscore',
        team_field: 'away_team'
      };
      second = {
        label: 'Home team',
        score_field: 'home_team_score',
        subscore_field: 'home_team_subscore',
        team_field: 'home_team'
      };
    }

    return (
      <Card title="Details" sectioned>
        <Formik
          ref={this.formikRef}
          initialValues={this.paramsToFormValues(data)}
          validationSchema={
            Yup.object().shape({
              start_at: Yup.date().required('Start date is required!'),
              home_team_score: Yup.number().required('Home team score is required!'),
              away_team_score: Yup.number().required('Away team score is required!'),
              status: Yup.string().required('Status is required')
            })
          }
          onSubmit={this.handleSubmit}
          render={({
            values,
            errors,
            status,
            handleBlur,
            touched,
            setFieldValue,
            handleSubmit,
            isSubmitting
          }) => (
            <Form onSubmit={handleSubmit}>
              <FormLayout>
                {status && <InlineError message={status} />}
                <Stack alignment="center">
                  <Stack.Item>
                    <Stack alignment="center">
                      <Stack.Item>
                        <Thumbnail source={data[first.team_field].logo} size="small" />
                      </Stack.Item>
                      <Stack.Item>
                        {data[first.team_field].name}
                      </Stack.Item>
                    </Stack>
                  </Stack.Item>
                  <Stack.Item>
                    <TextStyle variation="strong">
                      <div style={{ width: '100px', textAlign: 'center' }}>
                        {data.status === GAME_STATUS.ENDED && `${values[first.score_field]} : ${values[second.score_field]}`}
                      </div>
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item>
                    <Stack alignment="center">
                      <Stack.Item>
                        <Thumbnail source={data[second.team_field].logo} size="small" />
                      </Stack.Item>
                      <Stack.Item>
                        {data[second.team_field].name}
                      </Stack.Item>
                    </Stack>
                  </Stack.Item>
                </Stack>
                <Stack alignment="center">
                  <Stack.Item>
                    <Checkbox
                      label="Stop voting"
                      checked={values.stop_voting}
                      onChange={(value) => {
                        setFieldValue('stop_voting', value);
                      }}
                    />
                  </Stack.Item>
                  <Stack.Item>
                    <Checkbox
                      label="Set manually"
                      checked={values.setting_manually}
                      onChange={(value) => {
                        setFieldValue('setting_manually', value);
                      }}
                    />
                  </Stack.Item>
                  <Stack.Item>
                    <Checkbox
                      label="Null Spread and Over/Under"
                      checked={values.is_nulled}
                      onChange={(value) => {
                        setFieldValue('is_nulled', value);
                      }}
                    />
                  </Stack.Item>
                </Stack>
                <DatePicker
                  selected={values.start_at}
                  showTimeSelect
                  dateFormat="MM/dd/YYYY hh:mm a"
                  onBlur={handleBlur}
                  timeIntervals={5}
                  onChange={async (value) => {
                    await setFieldValue('start_at', value);
                  }}
                />
                {(touched.start_at && errors.start_at) && <InlineError message={errors.start_at} />}
                <Stack>
                  <Stack.Item fill>
                    <TextField
                      label={data[first.team_field].name + ' : Total score'}
                      type="text"
                      value={values[first.score_field]}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue([first.score_field], value);
                      }}
                      error={touched[first.score_field] && errors[first.score_field]}
                    />
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextField
                      label={data[second.team_field].name + ' : Total score'}
                      type="text"
                      value={values[second.score_field]}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue([second.score_field], value);
                      }}
                      error={touched[second.score_field] && errors[second.score_field]}
                    />
                  </Stack.Item>
                </Stack>
                <Stack>
                  <Stack.Item fill>
                    <TextField
                      label={data[first.team_field].name + ' : Score'}
                      type="text"
                      value={values[first.subscore_field]}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue([first.subscore_field], value);
                      }}
                      error={touched[first.subscore_field] && errors[first.subscore_field]}
                    />
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextField
                      label={data[second.team_field].name + ' : Score'}
                      type="text"
                      value={values[second.subscore_field]}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue([second.subscore_field], value);
                      }}
                      error={touched[second.subscore_field] && errors[second.subscore_field]}
                    />
                  </Stack.Item>
                </Stack>
                <FormLayout.Group>
                  <div>
                    <TextStyle>Status</TextStyle>
                    <Select
                      placeholder="Status"
                      classNamePrefix="react-select"
                      indicatorSeparator={null}
                      options={GAME_STATUS_OPTIONS}
                      value={values.status}
                      onBlur={handleBlur}
                      onChange={async (value) => {
                        await setFieldValue('status', value);
                      }}
                    />
                    {(touched.status && !!errors.status) && <InlineError message={errors.status} />}
                  </div>
                </FormLayout.Group>

                <div style={{ marginTop: '30px', fontSize: '16px' }}>
                  <strong>{data.between_players ? 'Player one' : 'Home team'}</strong>
                </div>
                <FormLayout.Group>
                  <TextField
                    label="Game detail"
                    placeholder="Game detail"
                    name="meta.home_team.game_detail"
                    multiline
                    value={values.meta.home_team.game_detail}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.home_team.game_detail', value);
                    }}
                  />
                  <TextField
                    label="Game detail ES"
                    placeholder="Game detail ES"
                    name="meta_es.home_team.game_detail"
                    multiline
                    value={values.meta_es.home_team.game_detail}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.home_team.game_detail', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="In"
                    placeholder="In"
                    name="meta.home_team.in"
                    multiline
                    value={values.meta.home_team.in}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.home_team.in', value);
                    }}
                  />
                  <TextField
                    label="In ES"
                    placeholder="In ES"
                    name="meta_es.home_team.in"
                    multiline
                    value={values.meta_es.home_team.in}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.home_team.in', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Out"
                    placeholder="Out"
                    name="meta.home_team.out"
                    multiline
                    value={values.meta.home_team.out}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.home_team.out', value);
                    }}
                  />
                  <TextField
                    label="Out ES"
                    placeholder="Out ES"
                    name="meta_es.home_team.out"
                    multiline
                    value={values.meta_es.home_team.out}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.home_team.out', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Questionable"
                    placeholder="Questionable"
                    name="meta.home_team.questionable"
                    multiline
                    value={values.meta.home_team.questionable}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.home_team.questionable', value);
                    }}
                  />
                  <TextField
                    label="Questionable ES"
                    placeholder="Questionable ES"
                    name="meta_es.home_team.questionable"
                    multiline
                    value={values.meta_es.home_team.questionable}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.home_team.questionable', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Betting Tips"
                    placeholder="Betting Tips"
                    name="meta.home_team.betting_tips"
                    multiline
                    value={values.meta.home_team.betting_tips}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.home_team.betting_tips', value);
                    }}
                  />
                  <TextField
                    label="Betting Tips ES"
                    placeholder="Betting Tips ES"
                    name="meta_es.home_team.betting_tips"
                    multiline
                    value={values.meta_es.home_team.betting_tips}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.home_team.betting_tips', value);
                    }}
                  />
                </FormLayout.Group>

                <div style={{ marginTop: '30px', fontSize: '16px' }}>
                  <strong>{data.between_players ? 'Player two' : 'Away team'}</strong>
                </div>
                <FormLayout.Group>
                  <TextField
                    label="Game detail"
                    placeholder="Game detail"
                    name="meta.away_team.game_detail"
                    multiline
                    value={values.meta.away_team.game_detail}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.away_team.game_detail', value);
                    }}
                  />
                  <TextField
                    label="Game detail ES"
                    placeholder="Game detail ES"
                    name="meta_es.away_team.game_detail"
                    multiline
                    value={values.meta_es.away_team.game_detail}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.away_team.game_detail', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="In"
                    placeholder="In"
                    name="meta.away_team.in"
                    multiline
                    value={values.meta.away_team.in}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.away_team.in', value);
                    }}
                  />
                  <TextField
                    label="In ES"
                    placeholder="In ES"
                    name="meta_es.away_team.in"
                    multiline
                    value={values.meta_es.away_team.in}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.away_team.in', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Out"
                    placeholder="Out"
                    name="meta.away_team.out"
                    multiline
                    value={values.meta.away_team.out}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.away_team.out', value);
                    }}
                  />
                  <TextField
                    label="Out ES"
                    placeholder="Out ES"
                    name="meta_es.away_team.out"
                    multiline
                    value={values.meta_es.away_team.out}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.away_team.out', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Questionable"
                    placeholder="Questionable"
                    name="meta.away_team.questionable"
                    multiline
                    value={values.meta.away_team.questionable}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.away_team.questionable', value);
                    }}
                  />
                  <TextField
                    label="Questionable ES"
                    placeholder="Questionable ES"
                    name="meta_es.away_team.questionable"
                    multiline
                    value={values.meta_es.away_team.questionable}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.away_team.questionable', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <TextField
                    label="Betting Tips"
                    placeholder="Betting Tips"
                    name="meta.away_team.betting_tips"
                    multiline
                    value={values.meta.away_team.betting_tips}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta.away_team.betting_tips', value);
                    }}
                  />
                  <TextField
                    label="Betting Tips ES"
                    placeholder="Betting Tips ES"
                    name="meta_es.away_team.betting_tips"
                    multiline
                    value={values.meta_es.away_team.betting_tips}
                    onBlur={handleBlur}
                    onChange={(value) => {
                      setFieldValue('meta_es.away_team.betting_tips', value);
                    }}
                  />
                </FormLayout.Group>
                <FormLayout.Group>
                  <Button
                    loading={isSubmitting}
                    submit
                    primary
                  >
                    Update
                  </Button>
                </FormLayout.Group>
              </FormLayout>
            </Form>
          )}
        />
      </Card>
    );
  }
}

View.defaultProps = {
  data: {},
  onUpdate: () => { }
};

export default View;
