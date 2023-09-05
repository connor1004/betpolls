import React, { Component } from 'react';
import {
  Card, Stack, Button, ButtonGroup, ResourceList, SkeletonBodyText, Thumbnail, TextStyle,
  Checkbox
} from '@shopify/polaris';
import moment from 'moment-timezone';

import OptionsHelper from '../../../helpers/OptionsHelper';
import { GAME_STATUS } from '../../../configs/enums';
import { GAME_STATUS_OPTIONS } from '../../../configs/options';

class Item extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDeleting: false,
      isPulling: false,
      showDeleteConfirm: false
    };
    this.formikRef = null;
    this.handleDelete = this.handleDelete.bind(this);
    this.handleToggleActive = this.handleToggleActive.bind(this);
    this.handleDetail = this.handleDetail.bind(this);
    this.handlePull = this.handlePull.bind(this);
    this.handleStatistics = this.handleStatistics.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  setPulling(isPulling) {
    this.setState({
      isPulling
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  async handleDelete() {
    const {
      onDelete, data
    } = this.props;
    await onDelete(data.id, this);
  }

  async handleToggleActive() {
    const {
      onToggleActive, data
    } = this.props;
    await onToggleActive(data.id, this);
  }

  handleDetail() {
    const {
      data, onDetail
    } = this.props;
    onDetail(data);
  }

  handleStatistics() {
    const {
      data, onStatistics
    } = this.props;
    onStatistics(data);
  }

  handleToggleSelectItem(item) {
    this.props.OnToggleSelectItem(item);
  }

  async handlePull() {
    const {
      onPull, data
    } = this.props;
    await onPull(data.id, this);
  }

  renderViewMode() {
    const {
      data, homeTeamFirst
    } = this.props;
    const {
      showDeleteConfirm, isDeleting, isPulling
    } = this.state;
    const {
      home_team, away_team, home_team_score, away_team_score, between_players
    } = data;

    let first;
    let second;
    if (homeTeamFirst) {
      first = {
        team: home_team,
        score: home_team_score
      };
      second = {
        team: away_team,
        score: away_team_score
      };
    } else {
      first = {
        team: away_team,
        score: away_team_score
      };
      second = {
        team: home_team,
        score: home_team_score
      };
    }

    const gameStatusOption = OptionsHelper.getOption(data.status, GAME_STATUS_OPTIONS);

    return (
      <div>
        <Card>
          <ResourceList.Item>
            {
              showDeleteConfirm ? (
                <Stack>
                  <Stack.Item fill>
                    Do you want to delete?
                  </Stack.Item>
                  <Stack.Item>
                    <ButtonGroup segmented>
                      <Button
                        primary
                        loading={isDeleting}
                        onClick={this.handleDelete}
                      >
                        Yes
                      </Button>
                      <Button
                        disabled={isDeleting}
                        onClick={this.toggleDeleteConfirm}
                      >
                        No
                      </Button>
                    </ButtonGroup>
                  </Stack.Item>
                </Stack>
              ) : (
                <Stack>
                  <Stack.Item>
                    <div style={{ width: '40px' }}>
                      <Checkbox
                        checked={data.selected}
                        onChange={this.handleToggleSelectItem.bind(this, data)}
                      />
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px' }}>{data.id}</div>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '60px' }}>{data.ref_id}</div>
                  </Stack.Item>
                  <Stack.Item>
                    <TextStyle>
                      <div style={{ width: '160px' }}>
                        {moment(moment.utc(data.start_at).toDate()).tz("America/New_York").format('DD/MM/YYYY hh:mm a')}
                      </div>
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '240px' }}>
                      <Stack>
                        <Stack.Item>
                          <Thumbnail source={first.team.logo} size="small" />
                        </Stack.Item>
                        <Stack.Item>
                          {first.team.name}
                        </Stack.Item>
                      </Stack>
                    </div>
                  </Stack.Item>
                  <Stack.Item>
                    <TextStyle variation="strong">
                      <div style={{ width: '80px' }}>
                        {
                          data.status === GAME_STATUS.ENDED && (
                            `${first.score} : ${second.score}`
                          )
                        }
                      </div>
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '240px' }}>
                      <Stack>
                        <Stack.Item>
                          <Thumbnail source={second.team.logo} size="small" />
                        </Stack.Item>
                        <Stack.Item>
                          {second.team.name}
                        </Stack.Item>
                      </Stack>
                    </div>
                  </Stack.Item>
                  <Stack.Item fill>
                    <TextStyle>
                      <div style={{ width: '100px' }}>
                        {gameStatusOption && gameStatusOption.label}
                      </div>
                    </TextStyle>
                  </Stack.Item>
                  <Stack.Item>
                    <div style={{ width: '190px' }}>
                      <ButtonGroup segmented>
                        <Button
                          primary
                          icon="products"
                          onClick={this.handleDetail}
                        />
                        <Button
                          primary
                          disabled={!data.game_bet_types || data.game_bet_types.length === 0}
                          icon="onlineStore"
                          onClick={this.handleStatistics}
                        />
                        <Button
                          primary
                          icon="refresh"
                          disabled={isPulling}
                          onClick={this.handlePull}
                        />
                        <Button
                          icon={data.deleted_at === null ? 'cancelSmall' : 'checkmark'}
                          primary={data.deleted_at !== null}
                          destructive={data.deleted_at === null}
                          onClick={this.handleToggleActive}
                        />
                        <Button
                          destructive
                          icon="delete"
                          onClick={this.toggleDeleteConfirm}
                        />
                      </ButtonGroup>
                    </div>
                  </Stack.Item>
                </Stack>
              )
            }
          </ResourceList.Item>
        </Card>
      </div>
    );
  }

  render() {
    const {
      isLoading
    } = this.props;
    if (isLoading) {
      return (
        <Card>
          <ResourceList.Item>
            <SkeletonBodyText />
          </ResourceList.Item>
        </Card>
      );
    }
    return this.renderViewMode();
  }
}

Item.defaultProps = {
  homeTeamFirst: false,
  isLoading: false,
  onToggleActive: () => { },
  onPull: () => { },
  onDelete: () => { },
  onDetail: () => { },
  onStatistics: () => { }
};

export default Item;
